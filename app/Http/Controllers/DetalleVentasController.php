<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;

class DetalleVentasController extends Controller
{
    //index
    public function index(){

        $ventas = Venta::select(
            'ventas.*',
            'users.name as nombre_usuario'
        )
        ->join('users', 'ventas.user_id', '=', 'users.id')
        ->orderBy('ventas.created_at', 'desc')
        ->get();

        return view('modulos.detalleventas.index', compact('ventas'));

    }

}
