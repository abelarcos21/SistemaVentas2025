<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    //index

    public function index(){

        return view('modulos.categorias.index');

    }

}
