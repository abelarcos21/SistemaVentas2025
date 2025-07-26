<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ProductoController extends Controller{

    public function index(){
        return response()->json(Producto::all()); //para movil flutter mostrar todos los productos
    }

    /* public function index(){

        try {
            $productos = Producto::select(
                'productos.id',
                'productos.nombre',
                'productos.descripcion',
                'productos.precio_compra',
                'productos.precio_venta',
                'productos.cantidad',
                'productos.moneda',
                'productos.codigo',
                'productos.barcode_path',
                'productos.categoria_id',
                'productos.proveedor_id',
                'productos.marca_id',
                'productos.created_at',
                'productos.activo',
                'categorias.nombre as nombre_categoria',
                'proveedores.nombre as nombre_proveedor',
                'marcas.nombre as nombre_marca',
                'imagens.ruta as imagen_producto',
                'imagens.id as imagen_id'
            )
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->join('proveedores', 'productos.proveedor_id', '=', 'proveedores.id')
            ->join('marcas', 'productos.marca_id', '=', 'marcas.id')
            ->leftJoin('imagens', 'productos.id', '=', 'imagens.producto_id')
            ->with('monedas')
            ->get();


            return response()->json([
                'success' => true,
                'data' => $productos,
                'message' => 'Productos cargados correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cargar productos: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar productos',
                'error' => $e->getMessage()
            ], 500);
        }
    } */

   /*  public function getFormData(){
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'categorias' => Categoria::all(),
                    'proveedores' => Proveedor::all(),
                    'marcas' => Marca::all()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cargar datos del formulario: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar datos del formulario'
            ], 500);
        }
    } */

    /* public function toggleEstado(Request $request, $id){
        try {
            $request->validate([
                'activo' => 'required|boolean'
            ]);

            $producto = Producto::findOrFail($id);
            $producto->activo = $request->activo;
            $producto->save();

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'data' => [
                    'id' => $producto->id,
                    'activo' => $producto->activo
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar estado del producto: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado'
            ], 500);
        }
    } */

    public function store(Request $request){

        $validated = $request->validate([
        'nombre' => 'required|string',
        'precio' => 'required|numeric',
        'stock' => 'required|integer',
        ]);

        $producto = Producto::create($validated);

        return response()->json($producto, 201);

    }

    public function show($id){
        return response()->json(Producto::findOrFail($id));
    }

    public function update(Request $request, $id){
        $product = Producto::findOrFail($id);
        $product->update($request->all());
        return response()->json($product);
    }

    public function destroy($id){
        Producto::destroy($id);
        return response()->json(null, 204);
    }

}
