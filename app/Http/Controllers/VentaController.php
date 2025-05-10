<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class VentaController extends Controller
{
    //index

    public function index(){

        $productos = Producto::all();
        return view('modulos.ventas.index', compact('productos'));
    }

}
