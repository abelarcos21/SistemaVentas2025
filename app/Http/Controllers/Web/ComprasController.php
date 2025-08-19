<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
use App\Models\Compra;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Auth;

class ComprasController extends Controller
{
    //metodo index
    public function index(){

        $compras = Compra::select(
            'compras.*',
            'users.name as nombre_usuario',
            'productos.nombre as nombre_producto'
        )
        ->join('users', 'compras.user_id', '=', 'users.id')
        ->join('productos', 'compras.producto_id', '=' , 'productos.id')
        ->get();

        return view('modulos.compras.index', compact('compras'));

    }


    public function create($id)
    {
        $producto = Producto::findOrFail($id);
        return view('modulos.compras.create', compact('producto'));
    }

    // Método para mostrar el modal (vista parcial)
    public function createModal($id){
        try {
            $producto = Producto::findOrFail($id);

            // Obtener el último precio de compra si existe
            $ultimaCompra = Compra::where('producto_id', $id)
                                 ->orderBy('created_at', 'desc')
                                 ->first();

            return view('modulos.compras.partials.compra-modal', compact('producto', 'ultimaCompra'));
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado: ' . $th->getMessage()
            ], 404);
        }
    }

    public function show(Compra $compra){

        $compra = Compra::select(
            'compras.*',
            'users.name as nombre_usuario',
            'productos.nombre as nombre_producto'
        )
        ->join('users', 'compras.user_id', '=', 'users.id')
        ->join('productos', 'compras.producto_id', '=' , 'productos.id')
        ->where('compras.id', $compra->id)
        ->first();
        return view('modulos.compras.show', compact('compra'));

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


    public function destroy ($id, Request $request){

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

    }

}
