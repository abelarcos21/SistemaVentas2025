<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Categoria;

class VentaController extends Controller
{
    //index
    public function index(Request $request){

        $productos = Producto::paginate(12);

        if($request->ajax()){
            return view('modulos.productos.ajaxproductos', compact('productos'))->render();
        }


        $clientes = Cliente::orderBy('nombre')->get();

        /* $categorias = Categoria::withCount(['productos' => function ($query){
            $query->where('cantidad', '>', 0);

        }])->get(); */

        // NO pongas condiciones aquí para que no falle el conteo
        $categorias = Categoria::withCount('productos')->get();

        //$totalProductos = Producto::where('cantidad', '>', 0)->count();
        //Evita poner ->where('cantidad', '>', 0) dentro del withCount, a menos que estés 100%
        //seguro de que todos los productos tienen cantidad válida. Si alguno tiene null o cantidad = 0, no lo cuenta.
        $totalProductos = Producto::count(); // o ->where('cantidad', '>', 0)->count() si quieres filtrar

        return view('modulos.ventas.index', compact('totalProductos', 'productos', 'clientes','categorias'));
    }

}
