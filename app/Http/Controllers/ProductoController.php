<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductoController extends Controller
{
    //index
    public function index(){
        return view('modulos.productos.index');
    }
}
