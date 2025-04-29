<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;

class ProveedorController extends Controller
{
    //metodo index
    public function index(){

        $proveedores = Proveedor::all();

        return view('modulos.proveedores.index', compact('proveedores'));

    }
}
