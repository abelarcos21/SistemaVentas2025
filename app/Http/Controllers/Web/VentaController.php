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
    public function index(){

        $productos = Producto::all();
        $clientes = Cliente::orderBy('nombre')->get();
        $categorias = Categoria::all();
        return view('modulos.ventas.index', compact('productos', 'clientes','categorias'));
    }

}
