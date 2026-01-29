<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // IMPORTANTE: esta línea importa la clase base
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Auth;

class ComprasController extends Controller
{
    //metodo index
    public function index(Request $request){

        if ($request->ajax()) {

            // 1. Definimos la consulta base (con los mismos filtros que la tabla)
            $query = Compra::query();

            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                $query->whereBetween('created_at', [
                    $request->fecha_inicio . ' 00:00:00',
                    $request->fecha_fin . ' 23:59:59'
                ]);
            }

            // 2. Calculamos los totales para los Info-Boxes tarjetas visuales
            // Clonamos la query para que los cálculos no afecten la paginación de DataTable
            $totalCompras = (clone $query)->count();
            $totalGasto = (clone $query)->selectRaw('SUM(cantidad * precio_compra) as total')->value('total') ?? 0;

            return DataTables::of($query->with(['user', 'producto'])->select('compras.*'))
                ->addIndexColumn()
                // Usamos los nombres de las relaciones definidas en el Modelo Compra
                ->addColumn('nombre_usuario', function($row){
                    return $row->user->name ?? 'N/A';
                })
                ->addColumn('nombre_producto', function($row){
                    return $row->producto->nombre ?? 'N/A';
                })
                ->editColumn('cantidad', function ($compra) {
                    return '<span class="badge bg-primary">' . $compra->cantidad . '</span>';
                })
                ->editColumn('precio_compra', function ($compra) {
                    return '<span class="text-blue">$' . number_format($compra->precio_compra, 2) . '</span>';
                })
                ->addColumn('total_compra', function ($compra) {
                    return '<span class="text-blue">$' . number_format($compra->precio_compra * $compra->cantidad, 2) . '</span>';
                })
                ->editColumn('created_at', function ($compra) {
                    return $compra->created_at->format('d/m/Y h:i a');
                })
                ->addColumn('acciones', function ($compra) {

                    $showBtn = '<a href="' . route('compra.show', $compra->id) . '" class="btn bg-gradient-info btn-sm mr-1" title="Ver Detalles">
                                    <i class="fas fa-eye"></i>
                                </a>';

                    $editBtn = '<a href="' . route('compra.edit', $compra->id) . '" class="btn bg-gradient-primary btn-sm mr-1" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>';

                    $deleteBtn = '<button class="btn bg-gradient-danger btn-sm mr-1 delete-btn" data-id="' . $compra->id . '" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>';

                    return '<div class="d-flex">' . $showBtn . $editBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['cantidad', 'precio_compra', 'total_compra', 'acciones'])
                ->with([
                    'totalCompras' => number_format($totalCompras),
                    'totalGasto'   => number_format($totalGasto, 2)
                ])
                ->make(true);
        }

        return view('modulos.compras.index');
    }
    /* public function index(Request $request){

        if ($request->ajax()) {
            $compras = Compra::select(
                'compras.id',
                'compras.cantidad',
                'compras.precio_compra',
                'compras.created_at',
                'compras.user_id',
                'compras.producto_id',
                'users.name as nombre_usuario',
                'productos.nombre as nombre_producto'
            )
            ->join('users', 'compras.user_id', '=', 'users.id')
            ->join('productos', 'compras.producto_id', '=', 'productos.id');

            return DataTables::of($compras)
                ->addIndexColumn()
                ->editColumn('cantidad', function ($compra) {
                    return '<span class="badge bg-primary">' . $compra->cantidad . '</span>';
                })
                ->editColumn('precio_compra', function ($compra) {
                    return '<span class="text-blue">$' . number_format($compra->precio_compra, 2) . '</span>';
                })
                ->addColumn('total_compra', function ($compra) {
                    $total = $compra->precio_compra * $compra->cantidad;
                    return '<span class="text-blue">$' . number_format($total, 2) . '</span>';
                })
                ->editColumn('created_at', function ($compra) {
                    return $compra->created_at->format('d/m/Y h:i a');
                })
                ->addColumn('acciones', function ($compra) {
                    $editBtn = '<a href="' . route('compra.edit', $compra->id) . '" class="btn bg-gradient-primary btn-sm mr-1" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>';

                    $deleteBtn = '<button class="btn bg-gradient-danger btn-sm delete-btn" data-id="' . $compra->id . '" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>';

                    return '<div class="d-flex">' . $editBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['cantidad', 'precio_compra', 'total_compra', 'acciones'])
                ->make(true);
        }

        return view('modulos.compras.index');
    } */


    public function create($id)
    {
        $producto = Producto::findOrFail($id);
        return view('modulos.compras.create', compact('producto'));
    }

    // Método para mostrar el modal (vista parcial)
    public function createModal($id){
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
    }

   /*  public function show(Compra $compra){

        $compra = Compra::select(
            'compras.*',
            'users.name as nombre_usuario',
            'productos.nombre as nombre_producto',
            'productos.cantidad as cantidad_producto'
        )
        ->join('users', 'compras.user_id', '=', 'users.id')
        ->join('productos', 'compras.producto_id', '=' , 'productos.id')
        ->where('compras.id', $compra->id)
        ->first();
        return view('modulos.compras.show', compact('compra'));

    } */

    public function show(Compra $compra){
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
    }

    // Simplificamos edit y show usando la relación del modelo para aplicar refactorizado
   /*  public function edit(Compra $compra) {
        $compra->load(['user', 'producto']);
        return view('modulos.compras.edit', compact('compra'));
    } */

    public function update(Request $request, Compra $compra){
        /*
        Si ya hicimos una venta con este producto, no seria buena idea actualizarlo
        */

        $request->validate([
            'cantidad' => 'required|integer|min:1',
            'precio_compra' => 'required|numeric|min:0',
            'producto_id' => 'required|exists:productos,id',
        ]);

        DB::beginTransaction();


        try {

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
        }
    }


    public function store(Request $request){

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
    }

   /*  public function store(Request $request){ metodo por aplicar para refactorizar

        $request->validate([
            'id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'precio_compra' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction(); // Seguridad para el inventario
        try {
            $producto = Producto::findOrFail($request->id);

            $compra = Compra::create([
                'user_id' => Auth::id(),
                'producto_id' => $producto->id,
                'cantidad' => $request->cantidad,
                'precio_compra' => $request->precio_compra,
            ]);

            // Actualizar stock y último precio de compra
            $producto->increment('cantidad', $request->cantidad);
            $producto->update(['precio_compra' => $request->precio_compra]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Compra exitosa!']);
            }
            return to_route('producto.index')->with('success', 'Compra exitosa!');

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error en Compra Store: " . $th->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al procesar'], 500);
        }
    } */


   /*  public function destroy ($id, Request $request){

        try {

            // Buscar la compra por ID (no es necesario usar $request->id si ya lo pasaste como parámetro)
            $compra = Compra::findOrFail($id);
            $cantidad_compra = $compra->cantidad;

            // Buscar el producto
            $producto = Producto::find($request->producto_id);

            if (!$producto) {
                return to_route('compra.index')->with('error', 'Producto asociado no encontrado.');
            }

            // Verificar si al restar la cantidad no queda en negativo
            if ($producto->cantidad < $cantidad_compra) {
                return to_route('compra.index')->with('error', 'No se puede eliminar la compra porque dejaría el inventario del producto en negativo.');
            }

             // Eliminar la compra
            if ($compra->delete()) {
                // Actualizar la cantidad del producto
                $producto->cantidad -= $cantidad_compra;
                $producto->save();

                return to_route('compra.index')->with('success', 'Compra eliminada con éxito!');
            } else {
                return to_route('compra.index')->with('error', '¡La compra no se eliminó!');
            }
        } catch (\Throwable $th) {
            return to_route('compra.index')->with('error', 'No se pudo eliminar la compra. ' . $th->getMessage());
        }

    } */

    public function destroy($id){
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
    }


}
