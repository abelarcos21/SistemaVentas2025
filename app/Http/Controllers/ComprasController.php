<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\Producto;
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
            }

            return to_route('producto.index')->with('success', 'Compra exitosa!');
        } catch (\Throwable $th) {
            return to_route('producto.index')->with('error', 'No pudo comprar! ' . $th->getMessage());
        }
    }
}
