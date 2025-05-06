<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\Producto;

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
}
