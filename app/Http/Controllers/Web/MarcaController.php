<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base

class MarcaController extends Controller
{
    //
    public function index(){

        $marcas = Marca::all();
        return view('marcas.index', compact('marcas'));

    }
}
