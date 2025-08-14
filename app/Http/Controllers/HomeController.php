<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Proveedor;
use App\Models\Categoria;
use App\Models\Compra;

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
        $cantidadClientes = Cliente::count();
        $cantidadUsuarios = User::count();
        $cantidadProductos = Producto::count();
        $cantidadProveedores = Proveedor::count();
        $cantidadCategorias = Categoria::count();

        $productosBajoStock = Producto::where('cantidad', '<', 5)->get();
        $ventasRecientes = Venta::orderBy('created_at','desc')->take(5)->get();
        $comprasRecientes = Compra::orderBy('created_at', 'desc')->take(5)->get();
        return view('home', compact([

            'totalVentas',
            'cantidadVentas',
            'productosBajoStock',
            'ventasRecientes',
            'comprasRecientes',
            'cantidadClientes',
            'cantidadProductos',
            'cantidadUsuarios',
            'cantidadProveedores',
            'cantidadCategorias',

        ]));
    }
}
