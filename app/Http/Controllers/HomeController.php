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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
    public function index(){

        // Datos del dashboard
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

        // Productos próximos a vencer (30 días)
        $productosProximosVencer = Producto::proximosAVencer(30)
            ->with(['categoria', 'marca'])
            ->orderBy('fecha_caducidad', 'asc')
            ->get();
        
        // Productos vencidos
        $productosVencidos = Producto::vencidos()
            ->with(['categoria', 'marca'])
            ->orderBy('fecha_caducidad', 'asc')
            ->get();
        
        // Estadísticas de caducidad
        $estadisticasCaducidad = [
            'proximos_7_dias' => Producto::proximosAVencer(7)->count(),
            'proximos_15_dias' => Producto::proximosAVencer(15)->count(),
            'proximos_30_dias' => Producto::proximosAVencer(30)->count(),
            'vencidos' => Producto::vencidos()->count(),
        ];

        // DATOS PARA LA GRÁFICA
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Ventas por día
        $ventas = DB::table('ventas')
            ->join('detalle_venta', 'ventas.id', '=', 'detalle_venta.venta_id')
            ->select(
                DB::raw('DATE(ventas.created_at) as fecha'),
                DB::raw('SUM(detalle_venta.sub_total) as total')
            )
            ->where('ventas.estado', 'completada')
            ->whereBetween('ventas.created_at', [$startDate, $endDate])
            ->groupBy('fecha')
            ->pluck('total', 'fecha')
            ->toArray();

        // Compras por día
        $compras = DB::table('compras')
            ->select(
                DB::raw('DATE(compras.created_at) as fecha'),
                DB::raw('SUM(compras.cantidad * compras.precio_compra) as total')
            )
            ->whereBetween('compras.created_at', [$startDate, $endDate])
            ->groupBy('fecha')
            ->pluck('total', 'fecha')
            ->toArray();

        // Preparar arrays para Chart.js
        $dias = [];
        $dataVentas = [];
        $dataCompras = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
            $dias[] = $diasSemana[$date->dayOfWeek] . ' ' . $date->day;
            $key = $date->format('Y-m-d');
            $dataVentas[] = $ventas[$key] ?? 0;
            $dataCompras[] = $compras[$key] ?? 0;
        }

        return view('home', compact(
            'productosProximosVencer',
            'productosVencidos',
            'estadisticasCaducidad',
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
            'dias',           // ← AGREGADO para graficas
            'dataVentas',     // ←
            'dataCompras'     // ←
        ));
    }

}
