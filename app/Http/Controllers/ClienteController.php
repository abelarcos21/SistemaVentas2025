<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteController extends Controller
{
    //index
    public function index(){
        return view('modulos.clientes.index');
    }
}
