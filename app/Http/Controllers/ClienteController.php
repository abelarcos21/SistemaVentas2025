<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteController extends Controller
{
    //index
    public function index(){
        $clientes = Cliente::all();
        return view('modulos.clientes.index', compact('clientes'));
    }
}
