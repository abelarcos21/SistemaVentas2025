<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    //
    public function create(){
        return view('modulos.facturas.create');
    }
}
