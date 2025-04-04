<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VentaController extends Controller
{
    //index
    public function index(){
        return view('modulos.ventas.index');
    }
}
