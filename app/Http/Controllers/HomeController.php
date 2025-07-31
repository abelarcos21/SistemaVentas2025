<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Venta;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $totalVentas = Venta::sum('total_venta');
        $cantidadVentas = Venta::count();
        $productosBajoStock = Producto::where('cantidad', '<', 5)->get();
        $ventasRecientes = Venta::orderBy('created_at','desc')->take(5)->get();
        return view('home', compact('totalVentas', 'cantidadVentas','productosBajoStock','ventasRecientes'));
    }
}
