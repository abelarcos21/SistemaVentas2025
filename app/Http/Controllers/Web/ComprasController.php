<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // IMPORTANTE: esta línea importa la clase base
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ComprasController extends Controller
{
    //metodo index
    public function index(Request $request){
        if ($request->ajax()) {
            // Iniciamos la query sobre COMPRAS (no detalles)
            $query = Compra::with(['user', 'proveedor', 'detalles']); // Eager loading necesario

            // Filtros de Fecha (usando fecha_compra)
            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                $query->whereBetween('fecha_compra', [
                    $request->fecha_inicio,
                    $request->fecha_fin
                ]);
            }

            //Filtro por Estado (Opcional, pero recomendado para sumas de dinero)
            // Probablemente no quieras sumar compras "canceladas"
            $query->where('estado', '!=', 'cancelada');

            //Clonamos para calcular totales antes de que DataTables aplique paginación
            // NOTA: Sumamos directamente la columna 'total' de compras. Es mucho más rápido.
            $totalCompras = (clone $query)->count();
            $totalGasto = (clone $query)->sum('total');

            return DataTables::of($query)
                ->addIndexColumn() // Agrega columna de índice automático
                ->addColumn('fecha_formateada', function($row) {
                    return $row->fecha_compra->format('d/m/Y');
                })
                ->addColumn('proveedor_nombre', function($row) {
                    return $row->proveedor->nombre ?? 'N/A';
                })
                ->addColumn('total_formateado', function($row) {
                    return '$' . number_format($row->total, 2);
                })
                ->addColumn('estado_badge', function($row) {
                    $badgeClass = match($row->estado) {
                        'completada' => 'success',
                        'cancelada' => 'danger',
                        default => 'warning'
                    };
                    return '<span class="badge bg-' . $badgeClass . '">' . ucfirst($row->estado) . '</span>';
                })
                ->addColumn('acciones', function($row) {

                    $buttons = '<a href="' . route('compras.show', $row) . '"
                                    class="btn btn-sm btn-info mr-1" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>';

                    // COMPRA PENDIENTE
                    if ($row->estado === 'pendiente') {

                        $buttons .= '<a href="' . route('compras.edit', $row) . '"
                                        class="btn btn-sm btn-primary mr-1" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>';

                        $buttons .= '<button data-id="'.$row->id.'"
                                        class="btn btn-sm btn-success mr-1 completar-btn"
                                        title="Completar">
                                        <i class="fas fa-check"></i>
                                    </button>';

                        $buttons .= '<button
                                        data-url="'.route('compras.cancelar', $row->id).'"
                                        class="btn btn-sm btn-danger cancelar-btn">
                                        <i class="fas fa-ban"></i>
                                    </button>';
                    }

                    // COMPRA COMPLETADA
                    if ($row->estado === 'completada') {

                        $buttons .= '<button
                                        data-url="'.route('compras.cancelar', $row->id).'"
                                        class="btn btn-sm btn-danger cancelar-btn">
                                        <i class="fas fa-ban"></i>
                                    </button>';
                    }

                    return '<div class="btn-group">'.$buttons.'</div>';
                })
                ->filterColumn('proveedor_nombre', function($query, $keyword) {
                    $query->whereHas('proveedor', function($q) use ($keyword) {
                        $q->where('nombre', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['estado_badge', 'acciones'])
                ->with([
                    'totalCompras' => $totalCompras,
                    'totalGasto'   => number_format($totalGasto, 2)
                ])
                ->make(true);
        }

        return view('modulos.compras.index');
    }

    public function create(Request $request){

        $productos = Producto::where('activo', true)->get();
        $proveedores = Proveedor::where('activo', true)->get();


        $proveedorSugerido = null;
        $productoPrecargado = null;
        $precioSugerido = null;

        //Si viene producto desde botón Reabastecer
        if ($request->has('producto')) {

            $productoPrecargado = Producto::with([
                'ultimaCompra.compra.proveedor'
            ])->findOrFail($request->producto);

            if ($productoPrecargado->ultimaCompra) {
                $precioSugerido = $productoPrecargado->ultimaCompra->precio_unitario;
                $proveedorSugerido = $productoPrecargado->ultimaCompra->compra->proveedor;
            } else {
                $precioSugerido = $productoPrecargado->precio_compra;
                $proveedorSugerido = $productoPrecargado->proveedor;
            }
        }
        return view('modulos.compras.create', compact(
            'productos',
            'proveedores',
            'productoPrecargado',
            'precioSugerido',
            'proveedorSugerido'
        ));
    }

    public function store(Request $request){

        $validated = $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'numero_factura' => 'nullable|string|unique:compras',
            'fecha_compra' => 'required|date',
            'metodo_pago' => 'required|in:efectivo,transferencia,credito', // Validamos las opciones exactas
            'impuesto' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',

            // ¡IMPORTANTE! 'distinct' evita que el usuario envíe el mismo producto en dos filas diferentes,
            // lo cual respeta tu nueva regla $table->unique(['compra_id', 'producto_id']);
            'detalles.*.producto_id' => 'required|exists:productos,id|distinct',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ], [
            // Mensaje personalizado amigable para la regla distinct
            'detalles.*.producto_id.distinct' => 'Has agregado el mismo producto más de una vez. Por favor, suma las cantidades en una sola fila.',
        ]);

        DB::beginTransaction();

        try {

            // Uso de Colecciones para calcular el total de forma segura
            $detalles = collect($request->detalles);

            $subtotalCompra = $detalles->sum(function ($item) {
                return $item['cantidad'] * $item['precio_unitario'];
            });

            $impuesto = $request->impuesto ?? 0;
            $descuento = $request->descuento ?? 0;

            // Si el impuesto en el sistema es un porcentaje (ej. 16%), el cálculo sería diferente.
            // Aquí asumo que es un monto directo en dinero, según la vista create
            $totalFinal = $subtotalCompra + $impuesto - $descuento;

            //Crear la compra guardando los totales reales
            $compra = Compra::create([
                'user_id' => auth()->id(),
                'proveedor_id' => $request->proveedor_id,
                'numero_factura' => $request->numero_factura,
                'fecha_compra' => $request->fecha_compra,
                'metodo_pago' => $request->metodo_pago,
                'subtotal' => $subtotalCompra,
                'descuento' => $descuento,
                'impuesto' => $impuesto,
                'total' => $totalFinal,
                'estado' => 'pendiente', //dependiendo el flujo de negocio
                'observaciones' => $request->observaciones,
            ]);

            //Guardar detalles y actualizar stock de forma segura
            foreach ($detalles as $detalle) {

                $subtotalDetalle = $detalle['cantidad'] * $detalle['precio_unitario'];

                $compra->detalles()->create([
                    'producto_id'     => $detalle['producto_id'],
                    'cantidad'        => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal'        => $subtotalDetalle,
                ]);

                // ¡Optimización Clave! Actualizar stock a nivel de SQL directamente
                // increment() previene Race Conditions.
                // El tercer parámetro actualiza el ultimo costo de columnas adicionales en la misma consulta.
                Producto::where('id', $detalle['producto_id'])
                    ->increment('cantidad', $detalle['cantidad'], [
                        'precio_compra' => $detalle['precio_unitario']
                    ]);
            }

            DB::commit();

            return redirect()->route('compras.show', $compra)
                ->with('success', 'Compra creada exitosamente');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with('error', 'Error al crear la compra: ' . $e->getMessage())
                ->withInput();
        }
    }

   /*  public function create($id)
    {
        $producto = Producto::findOrFail($id);
        return view('modulos.compras.create', compact('producto'));
    } */

    // Método para mostrar el modal (vista parcial)
    /* public function createModal($id){
        try {
            $producto = Producto::findOrFail($id);

            $proveedores = Proveedor::all();

            // Obtener el último precio de compra si existe
            $ultimaCompra = Compra::where('producto_id', $id)
                                 ->orderBy('created_at', 'desc')
                                 ->first();

            return view('modulos.compras.partials.compra-modal', compact('producto', 'proveedores', 'ultimaCompra'));
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado: ' . $th->getMessage()
            ], 404);
        }
    } */

    public function show(Compra $compra){
        $compra->load(['user', 'proveedor', 'detalles.producto']);
        return view('modulos.compras.show', compact('compra'));
    }

    public function edit(Compra $compra){
        if (!$compra->isPendiente()) {
            return back()->with('error', 'Solo se pueden editar compras pendientes');
        }

        $productos = Producto::where('activo', true)->get();
        $proveedores = Proveedor::where('activo', true)->get();
        $compra->load('detalles.producto');

        return view('modulos.compras.edit', compact('compra', 'productos', 'proveedores'));
    }

    public function update(Request $request, Compra $compra){

        if (!$compra->isPendiente()) {
            return back()->with('error', 'Solo se pueden editar compras pendientes');
        }

        $validated = $request->validate([
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'numero_factura' => 'nullable|unique:compras,numero_factura,' . $compra->id,
            'fecha_compra' => 'required|date',
            'impuesto' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {

            // Revertir stock anterior
            foreach ($compra->detalles as $detalleAnterior) {

                $producto = Producto::find($detalleAnterior->producto_id);

                if ($producto) {
                    $producto->cantidad -= $detalleAnterior->cantidad;
                    $producto->save();
                }
            }

            // Eliminar detalles anteriores
            $compra->detalles()->delete();

            // Actualizar cabecera
            $compra->update([
                'proveedor_id' => $request->proveedor_id,
                'numero_factura' => $request->numero_factura,
                'fecha_compra' => $request->fecha_compra,
                'impuesto' => $request->impuesto ?? 0,
                'observaciones' => $request->observaciones,
            ]);

            // Crear nuevos detalles y aplicar stock
            foreach ($request->detalles as $detalle) {

                $subtotal = $detalle['cantidad'] * $detalle['precio_unitario'];

                $compra->detalles()->create([
                    'producto_id'     => $detalle['producto_id'],
                    'cantidad'        => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal'        => $subtotal,
                ]);

                $producto = Producto::find($detalle['producto_id']);

                if ($producto) {
                    $producto->cantidad += $detalle['cantidad'];
                    $producto->precio_compra = $detalle['precio_unitario'];
                    $producto->proveedor_id = $request->proveedor_id;
                    $producto->save();
                }
            }

            DB::commit();

            return redirect()->route('compras.show', $compra)
                ->with('success', 'Compra actualizada exitosamente');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Error al actualizar la compra: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function cancelar(Compra $compra){

        if ($compra->estado !== 'completada') {
            return response()->json([
                'error' => 'Solo se pueden cancelar compras completadas'
            ], 400);
        }

        DB::beginTransaction();

        try {

            foreach ($compra->detalles as $detalle) {

                $producto = Producto::find($detalle->producto_id);

                if ($producto) {
                    $producto->cantidad -= $detalle->cantidad;
                    $producto->save();
                }
            }

            $compra->estado = 'cancelada';
            $compra->save();

            DB::commit();

            return response()->json([
                'success' => 'Compra cancelada correctamente'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'error' => 'Error al cancelar la compra'
            ], 500);
        }
    }


    public function completar(Compra $compra){
        if (!$compra->isPendiente()) {
            return back()->with('error', 'Esta compra ya fue procesada');
        }

        $compra->completar();

        return back()->with('success', 'Compra completada exitosamente');
    }

    public function generarPDF(Compra $compra){
        $compra->load(['user', 'proveedor', 'detalles.producto']);

        $pdf = Pdf::loadView('modulos.compras.pdf', compact('compra'))
            ->setPaper('letter', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

        $nombreArchivo = 'compra-' . str_pad($compra->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->stream($nombreArchivo);
        // O si prefieres descargar:
        // return $pdf->download($nombreArchivo);
    }


    /* public function show(Compra $compra){
        // Cargamos las relaciones para evitar consultas extra en la vista
        $compra->load(['user', 'producto']);

        // Calculamos el total directamente para pasarlo a la vista
        $total = $compra->cantidad * $compra->precio_compra;

        return view('modulos.compras.show', compact('compra', 'total'));
    }

    public function edit(Compra $compra){

        $compra = Compra::select(
            'compras.*',
            'users.name as nombre_usuario',
            'productos.nombre as nombre_producto'
        )
        ->join('users', 'compras.user_id', '=', 'users.id')
        ->join('productos', 'compras.producto_id', '=' , 'productos.id')
        ->where('compras.id', $compra->id)
        ->first();
        return view('modulos.compras.edit', compact('compra'));
    } */

    /* public function update(Request $request, Compra $compra){
        /*
        Si ya hicimos una venta con este producto, no seria buena idea actualizarlo
        */

        /* $request->validate([
            'cantidad' => 'required|integer|min:1',
            'precio_compra' => 'required|numeric|min:0',
            'producto_id' => 'required|exists:productos,id',
        ]); */

        /* DB::beginTransaction(); */


        /* try {

            // Guardar la cantidad actual para ajustar el inventario
            $cantidad_anterior = $compra->cantidad;

            // Actualizar los datos de la compra
            $compra->cantidad = $request->cantidad;
            $compra->precio_compra = $request->precio_compra;


            if ($compra->save()) {
                // Ajustar inventario del producto
                $producto = Producto::find($request->producto_id);

                // Calcular la nueva cantidad del producto
                $nueva_cantidad = ($producto->cantidad - $cantidad_anterior) + $request->cantidad;

                // Validar que la nueva cantidad no sea negativa
                if ($nueva_cantidad < 0) {

                    // Cancelar si la cantidad sería negativa
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', 'La cantidad resultante del producto no puede ser negativa.');
                }

                $producto->cantidad = $nueva_cantidad;
                $producto->save();

                DB::commit();
                return to_route('compra.index')->with('success', 'Compra actualizada con éxito!');
            }

            return to_route('compra.index')->with('error', 'Ocurrió un error al Actualizar la compra.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return to_route('compra.index')->with('error', 'No pudo actualizar la comprar!' . $th->getMessage());
        } */
    /* }  */


    /* public function store(Request $request){

        $request->validate([
            'id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'precio_compra' => 'required|numeric|min:0'
        ]);

        try {
            $producto = Producto::findOrFail($request->id);

            $compra = new Compra();
            $compra->user_id = Auth::id();
            $compra->producto_id = $producto->id;
            $compra->cantidad = $request->cantidad;
            $compra->precio_compra = $request->precio_compra;

            if ($compra->save()) {
                // Actualizar stock del producto
                $producto->cantidad += $request->cantidad;
                $producto->precio_compra = $request->precio_compra;
                $producto->save();

                // Verificar si es una petición AJAX
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Compra exitosa!',
                        'data' => [
                            'total_compra' => number_format($compra->cantidad * $compra->precio_compra, 2),
                            'nuevo_stock' => $producto->cantidad,
                            'compra_id' => $compra->id
                        ]
                    ]);
                }

                // Si no es AJAX, redirigir normalmente
                return to_route('producto.index')->with('success', 'Compra exitosa!');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Errores de validación
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }

            return to_route('producto.index')->with('error', 'Error de validación');

        } catch (\Throwable $th) {
            // Error general
            \Log::error('Error al procesar compra: ' . $th->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pudo comprar! ' . $th->getMessage()
                ], 500);
            }

            return to_route('producto.index')->with('error', 'No pudo comprar! ' . $th->getMessage());
        }
    } */

   /*  public function destroy($id){
        DB::beginTransaction();
        try {
            $compra = Compra::findOrFail($id);
            $producto = $compra->producto;

            // Validación de stock antes de eliminar
            if ($producto->cantidad < $compra->cantidad) {
                return response()->json(['success' => false, 'message' => 'Inconsistencia: El stock quedaría en negativo.'], 422);
            }

            $producto->decrement('cantidad', $compra->cantidad);
            $compra->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Compra eliminada y stock actualizado.']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $th->getMessage()], 500);
        }
    } */


}
