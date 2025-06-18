<?php

namespace App\Http\Controllers\Facturacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
class ClienteController extends Controller
{
    //
    public function index() {
        $clientes = Cliente::all();
        return view('modulos.facturas.catalogos.clientes.index', compact('clientes'));
    }
}
