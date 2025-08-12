<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class POSController extends Controller
{

    //MOSTRAR LA VISTA DE ESCANER PARA MOSTRAR LA CAMARA DEL PC O LAPTOP
    public function index(){
        return view('modulos.pos.index');
    }
}
