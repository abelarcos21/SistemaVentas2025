<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DetalleVentasController extends Controller
{
    //index

    public function index(){

        return view('modulos.detalleventas.index');

    }
}
