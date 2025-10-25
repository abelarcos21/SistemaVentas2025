<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Cotizacion;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\CotizacionDetalle;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Caja;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class CotizacionController extends Controller
{

    /**
     * Mostrar listado de cotizaciones
     */
    public function index(Request $request){

       /*  $cotizaciones = Cotizacion::with('cliente')
            ->when($request->cliente_id, fn($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
            ->latest()
            ->paginate(10);

        $clientes = Cliente::all();

        return view('modulos.cotizaciones.index', compact('cotizaciones')); */

        if ($request->ajax()) {

            $cotizaciones = Cotizacion::with('cliente')->select('cotizaciones.*');

            return DataTables::eloquent($cotizaciones)
                ->addIndexColumn()
                ->addColumn('cliente', function ($cotizacion) {
                    return $cotizacion->cliente->nombre;
                })
                ->addColumn('total', function ($cotizacion) {
                    return '$' . number_format($cotizacion->total, 2);
                })
                ->addColumn('estado', function ($cotizacion) {
                    $badgeClass = match($cotizacion->estado) {
                        'pendiente' => 'badge-warning',
                        'convertida' => 'badge-success',
                        'cancelada' => 'badge-danger',
                        default => 'badge-secondary'
                    };

                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($cotizacion->estado) . '</span>';
                })
                ->addColumn('acciones', function ($cotizacion) {
                    $acciones = '
                        <div class="d-flex justify-content-center gap-1" style="gap: 0.25rem;">
                            <a href="' . route('cotizaciones.show', $cotizacion) . '" class="btn bg-gradient-info btn-sm" title="Ver Detalle">
                                <i class="fas fa-eye"></i> Ver Detalle
                            </a>
                            <a href="' . route('cotizaciones.edit', $cotizacion) . '" class="btn bg-gradient-warning btn-sm" title="Editar">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a target="_blank" href="' . route('cotizaciones.pdf', $cotizacion->id) . '" class="btn bg-gradient-secondary btn-sm" title="Ver PDF">
                                <i class="fas fa-print"></i> Ver PDF
                            </a>';

                            if ($cotizacion->estado === 'pendiente') {
                                $acciones .= '
                                    <a href="' . route('cotizaciones.convertir', $cotizacion->id) . '"
                                    class="btn bg-gradient-success btn-sm"
                                    title="Convertir a Venta">
                                        <i class="fas fa-cash-register"></i> Convertir a Venta
                                    </a>';
                            }

                            if ($cotizacion->estado !== 'cancelada') {
                                $acciones .= '
                                    <button type="button" class="btn bg-gradient-danger btn-sm btn-cancelar"
                                        data-id="' . $cotizacion->id . '"
                                        data-url="' . route('cotizaciones.destroy', $cotizacion) . '"
                                        title="Cancelar Cotizacion">
                                        <i class="fas fa-trash"></i> Cancelar
                                    </button>';
                            }

                        $acciones .= '</div>';

                    return $acciones;
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('cliente_id') && $request->cliente_id != '') {
                        $query->where('cliente_id', $request->cliente_id);
                    }

                    if ($request->has('estado') && $request->estado != '') {
                        $query->where('estado', $request->estado);
                    }

                    if ($request->has('fecha_desde') && $request->fecha_desde != '') {
                        $query->whereDate('fecha', '>=', $request->fecha_desde);
                    }

                    if ($request->has('fecha_hasta') && $request->fecha_hasta != '') {
                        $query->whereDate('fecha', '<=', $request->fecha_hasta);
                    }
                })
                ->rawColumns(['estado', 'acciones'])
                ->make(true);
        }

        $clientes = Cliente::orderBy('nombre')->get();

        return view('modulos.cotizaciones.index', compact('clientes'));
    }

    /**
     * Formulario de creación de cotización
    */
    public function create(){
        $clientes = Cliente::all();
        $productos = Producto::all();
        return view('modulos.cotizaciones.create', compact('clientes', 'productos'));
    }

    // Mostrar formulario de conversión (aquí se pueden modificar productos)
    public function mostrarFormularioConversion($id){
        $cotizacion = Cotizacion::with('detalles.producto')->findOrFail($id);

        if ($cotizacion->estado !== 'pendiente') {
            return back()->with('error', 'Esta cotización ya fue procesada.');
        }

        // Verificar caja abierta
        $caja = Caja::getCajaActivaByUser(auth()->id());
        if (!$caja) {
            return back()->with('error', 'Debes tener una caja abierta para convertir cotización en venta.');
        }

        $productos = Producto::where('cantidad', '>', 0)->get();

        return view('modulos.cotizaciones.convertir', compact('cotizacion', 'productos', 'caja'));
    }



    // Procesar la conversión con los productos modificados
    public function convertirEnVenta(Request $request, $id){

        $cotizacion = Cotizacion::with('detalles')->findOrFail($id);

        if ($cotizacion->estado !== 'pendiente') {
            return back()->with('error', 'La cotización ya fue procesada.');
        }

        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario_aplicado' => 'required|numeric|min:0',
        ]);

        // Buscar caja abierta
        $caja = Caja::getCajaActivaByUser(auth()->id());
        if (!$caja) {
            return back()->with('error', 'Debes tener una caja abierta para convertir cotización en venta.');
        }

        DB::beginTransaction();
        try {
            // Generar folio consecutivo
            $folio = \App\Models\Folio::lockForUpdate()->firstOrCreate(
                ['serie' => '001'],
                ['ultimo_numero' => 0]
            );
            $folio->ultimo_numero += 1;
            $folio->save();

            // Calcular total de los productos enviados
            $totalVenta = 0;
            foreach ($request->productos as $prod) {
                $totalVenta += $prod['cantidad'] * $prod['precio_unitario_aplicado'];
            }

            // Crear la venta con los productos modificados
            $venta = Venta::create([
                'user_id'      => auth()->id(),
                'cliente_id'   => $cotizacion->cliente_id,
                'empresa_id'   => 1,
                'caja_id'      => $caja->id,
                'total_venta'  => $totalVenta,
                'folio'        => $folio->serie . '-' . str_pad($folio->ultimo_numero, 6, '0', STR_PAD_LEFT),
            ]);

            // Crear detalles de venta y descontar inventario
            foreach ($request->productos as $prod) {
                $subtotal = $prod['cantidad'] * $prod['precio_unitario_aplicado'];

                DetalleVenta::create([
                    'venta_id'                 => $venta->id,
                    'producto_id'              => $prod['producto_id'],
                    'cantidad'                 => $prod['cantidad'],
                    'precio_unitario_aplicado' => $prod['precio_unitario_aplicado'],
                    'sub_total'                => $subtotal,
                ]);

                // Descontar inventario
                $producto = Producto::findOrFail($prod['producto_id']);

                // Validar stock disponible
                if ($producto->cantidad < $prod['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre}. Disponible: {$producto->cantidad}");
                }

                $producto->decrement('cantidad', $prod['cantidad']);
            }

            // Actualizar cotización
            $cotizacion->update(['estado' => 'convertida']);

            // Actualizar caja
            $caja->increment('total_ventas', $venta->total_venta);

            DB::commit();

            return redirect()
                ->route('detalleventas.index')
                ->with('success', 'Venta realizada correctamente. Nro de Venta: ' . $venta->folio);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar venta: ' . $e->getMessage());
        }
    }

    /**
     * Guardar cotización en BD
    */
    public function store(Request $request){

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos'  => 'required|array',
            'productos.*' => 'required|integer|exists:productos,id',
            'cantidades' => 'required|array',
            'precios'    => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            $productos  = $request->productos;   // array de IDs
            $cantidades = $request->cantidades;  // array de cantidades
            $precios    = $request->precios;     // array de precios unitarios

            $subtotal = 0;
            $detalles = [];

            foreach ($productos as $i => $productoId) {
                $cantidad = (int)($cantidades[$i] ?? 1);
                $precio   = (float)($precios[$i] ?? 0);
                $lineaTotal = $cantidad * $precio;

                $subtotal += $lineaTotal;

                $detalles[] = [
                    'producto_id'     => $productoId,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precio,
                    'total'           => $lineaTotal, // tu campo real en cotizacion_detalles
                ];
            }

            // Ajusta según tu lógica fiscal
            /*  $impuestos = $subtotal * 0.16; */
            /* $total     = $subtotal + $impuestos; */

            $total = $subtotal;

            // Crear la cotización
            $cotizacion = Cotizacion::create([

                'cliente_id' => $request->cliente_id,
                'user_id'    => auth()->id(),
                'fecha'      => now(),
                'subtotal'   => $subtotal,
                /* 'impuestos'  => $impuestos, */
                'total'      => $total,
                'estado'     => 'pendiente',
            ]);

            // Crear detalles
            foreach ($detalles as $detalle) {
                $cotizacion->detalles()->create($detalle);
            }
        });

        return redirect()->route('cotizaciones.index')->with('success', 'Cotización creada correctamente.');
    }


    /**
     * Ver detalle de una cotización
     */
    public function show($id){
        $cotizacion = Cotizacion::with(['cliente', 'detalles.producto'])->findOrFail($id);

        // Traer la empresa (siempre 1, o la que necesite)
        $empresa = Empresa::first();
        return view('modulos.cotizaciones.show', compact('cotizacion','empresa'));
    }

    /**
     * Formulario de edición de cotización
     */
    public function edit($id){
        $cotizacion = Cotizacion::with('detalles.producto')->findOrFail($id);
        $clientes = Cliente::all();
        $productos = Producto::all();

        return view('modulos.cotizaciones.edit', compact('cotizacion', 'clientes', 'productos'));
    }

    /**
     * Actualizar cotización
    */
    public function update(Request $request, $id){

        $cotizacion = Cotizacion::findOrFail($id);

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos'  => 'required|array',
            'productos.*' => 'required|integer|exists:productos,id',
            'cantidades' => 'required|array',
            'precios'    => 'required|array',
        ]);

        DB::transaction(function () use ($request, $cotizacion) {
            $productos  = $request->productos;   // array de IDs
            $cantidades = $request->cantidades;  // array de cantidades
            $precios    = $request->precios;     // array de precios unitarios

            $subtotal = 0;
            $detalles = [];

            foreach ($productos as $i => $productoId) {
                $cantidad = (int)($cantidades[$i] ?? 1);
                $precio   = (float)($precios[$i] ?? 0);
                $lineaTotal = $cantidad * $precio;

                $subtotal += $lineaTotal;

                $detalles[] = [
                    'cotizacion_id'   => $cotizacion->id,
                    'producto_id'     => $productoId,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precio,
                    'total'           => $lineaTotal, //aquí usas tu campo real
                ];
            }

            //Ajusta el cálculo de impuestos a tu lógica real (ejemplo IVA 16%)
           /* $impuestos = $subtotal * 0.16;
            $total     = $subtotal + $impuestos; */

            $total = $subtotal;

            // Actualizar la cotización
            $cotizacion->update([
                'cliente_id' => $request->cliente_id,
                'subtotal'   => $subtotal,
                /* 'impuestos'  => $impuestos, */
                'total'      => $total,
            ]);

            // limpiar detalles previos
            $cotizacion->detalles()->delete();

            // insertar nuevos
            foreach ($detalles as $detalle) {
                CotizacionDetalle::create($detalle);
            }
        });

        return redirect()->route('cotizaciones.index')->with('success', 'Cotización actualizada.');
    }

    /**
     * Cancelar cotización
    */
    public function destroy($id){
        try {
            $cotizacion = Cotizacion::findOrFail($id);

            if ($cotizacion->estado !== 'pendiente') {
                return response()->json([
                    'error' => 'Solo se pueden cancelar cotizaciones pendientes.'
                ], 400);
            }

            $cotizacion->update(['estado' => 'cancelada']);

            return response()->json([
                'success' => true,
                'message' => 'Cotización cancelada correctamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cancelar la cotización: ' . $e->getMessage()
            ], 500);
        }
    }


    public function descargarPdf($id){
        $cotizacion = Cotizacion::with(['cliente', 'detalles.producto'])->findOrFail($id);
        $empresa = Empresa::first(); // o datos fijos

        $pdf = Pdf::loadView('modulos.cotizaciones.pdf', compact('cotizacion', 'empresa'))
                ->setPaper('A4', 'portrait');

        return $pdf->stream('cotizacion_'.$cotizacion->id.'.pdf');
    }
}
