<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    //index
    public function index(){
        return view('modulos.usuarios.index');
    }
}
