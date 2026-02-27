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
        // Alerta de Stock
        $productosBajoStock = Producto::where('cantidad', '<', 5)->get();

        // Listados recientes
        $ventasRecientes = Venta::orderBy('created_at','desc')->take(5)->get();
        // Compras Recientes
        // Usamos 'with' para traer el nombre del proveedor y ordenamos por fecha_compra
        $comprasRecientes = Compra::with('proveedor')
            ->orderBy('fecha_compra', 'desc')
            ->take(5)
            ->get();

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

        // DATOS PARA LA GRÁFICA (Últimos 7 días)
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Ventas por día
        $ventas = DB::table('ventas')
            ->select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('SUM(total_venta) as total') // Asumiendo que existe esta columna
            )
            ->where('estado', 'completada')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('fecha')
            ->pluck('total', 'fecha')
            ->toArray();

        // Compras por día
        $compras = Compra::select(
                DB::raw('DATE(fecha_compra) as fecha'),
                DB::raw('SUM(total) as total')
            )
            ->where('estado', '!=', 'cancelada') //ignorar las canceladas
            ->whereBetween('fecha_compra', [$startDate, $endDate])
            ->groupBy('fecha')
            ->pluck('total', 'fecha')
            ->toArray();

        // Preparar arrays para Chart.js
        $dias = [];
        $dataVentas = [];
        $dataCompras = [];

        // Formato de fechas para coincidir con SQL (Y-m-d)
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            // Etiqueta visual para el eje X (Ej: "Lun 12")
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
