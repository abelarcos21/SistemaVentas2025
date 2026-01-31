<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CajaController extends Controller{

    /* public function index(){
        $cajas = Caja::with('usuario')->latest()->get();
        return view('modulos.cajas.index', compact('cajas'));
    } */

    /**
     * Mostrar el módulo de caja
     */
    public function index(Request $request){
        if ($request->ajax()) {
            $cajas = Caja::with('usuario')
                ->select('cajas.*')
                ->latest();

            return DataTables::eloquent($cajas)
                ->addIndexColumn()
                ->addColumn('usuario', function ($caja) {
                    return $caja->usuario->name;
                })
                ->addColumn('apertura', function ($caja) {
                    return \Carbon\Carbon::parse($caja->apertura)->format('d/m/Y h:i a');
                })
                ->addColumn('cierre', function ($caja) {
                    return $caja->cierre
                        ? \Carbon\Carbon::parse($caja->cierre)->format('d/m/Y h:i a')
                        : '-';
                })
                ->addColumn('monto_inicial', function ($caja) {
                    return '$' . number_format($caja->monto_inicial, 2);
                })
                ->addColumn('total_ventas', function ($caja) {
                    return '$' . number_format($caja->total_ventas, 2);
                })
                ->addColumn('total_ingresos', function ($caja) {
                    return '$' . number_format($caja->total_ingresos, 2);
                })
                ->addColumn('total_egresos', function ($caja) {
                    return '$' . number_format($caja->total_egresos, 2);
                })
                ->addColumn('monto_final', function ($caja) {
                    return $caja->monto_final
                        ? '$' . number_format($caja->monto_final, 2)
                        : '-';
                })
                ->addColumn('diferencia', function ($caja) {
                    if ($caja->diferencia !== null) {
                        $class = $caja->diferencia >= 0 ? 'text-success' : 'text-danger';
                        return '<span class="' . $class . '">$' . number_format($caja->diferencia, 2) . '</span>';
                    }
                    return '-';
                })
                ->addColumn('estado', function ($caja) {
                    $badgeClass = $caja->estado == 'abierta' ? 'badge-success' : 'badge-secondary';
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($caja->estado) . '</span>';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('estado') && $request->estado != '') {
                        $query->where('estado', $request->estado);
                    }

                    if ($request->has('usuario_id') && $request->usuario_id != '') {
                        $query->where('user_id', $request->usuario_id);
                    }

                    if ($request->has('fecha_desde') && $request->fecha_desde != '') {
                        $query->whereDate('apertura', '>=', $request->fecha_desde);
                    }

                    if ($request->has('fecha_hasta') && $request->fecha_hasta != '') {
                        $query->whereDate('apertura', '<=', $request->fecha_hasta);
                    }
                })
                ->rawColumns(['diferencia', 'estado'])
                ->make(true);
        }

        $cajaAbierta = Caja::where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->first();

        // Calcular el total esperado aquí para enviarlo listo a la vista
        $totalEsperado = 0;

        if ($cajaAbierta) {
            $totalEsperado = $cajaAbierta->monto_inicial + $cajaAbierta->total_ventas + $cajaAbierta->total_ingresos - $cajaAbierta->total_egresos;
        }

        return view('modulos.cajas.index', compact('cajaAbierta', 'totalEsperado'));
    }

    public function abrir(Request $request){
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        // validar que el usuario no tenga una caja abierta
        if (Caja::where('user_id', Auth::id())->where('estado', 'abierta')->exists()) {
            return back()->with('error', 'Ya tienes una caja abierta.');
        }

        Caja::create([
            'user_id' => Auth::id(),
            'monto_inicial' => $request->monto_inicial,
            'apertura' => now(),
        ]);

        return back()->with('success', 'Caja abierta correctamente.');
    }

    public function cerrar(Request $request, Caja $caja){
        $request->validate([
            'monto_final' => 'required|numeric|min:0',
        ]);

        // Verificar que sea el dueño
        if ($caja->user_id != Auth::id()) {
            return back()->with('error', 'No puedes cerrar esta caja');
        }

        if ($caja->estado == 'cerrada') {
            return back()->with('error', 'La caja ya está cerrada');
        }

        $esperado = $caja->monto_inicial + $caja->total_ventas + $caja->total_ingresos - $caja->total_egresos;
        $diferencia = $request->monto_final - $esperado;

        $caja->update([
            'monto_final' => $request->monto_final,
            'diferencia' => $diferencia,
            'cierre' => now(),
            'estado' => 'cerrada',
        ]);

        return back()->with('success', 'Caja cerrada correctamente.');
    }

    public function movimiento(Request $request, Caja $caja){

        $request->validate([
            'tipo' => 'required|in:ingreso,egreso',
            'monto' => 'required|numeric|min:1',
            'descripcion' => 'nullable|string|max:255'
        ]);

        if ($caja->estado == 'cerrada') {
            return back()->with('error', 'No se pueden hacer movimientos en una caja cerrada');
        }

        if ($request->tipo == 'ingreso') {
            $caja->increment('total_ingresos', $request->monto);
        } else {
            $caja->increment('total_egresos', $request->monto);
        }

        MovimientoCaja::create([
            'caja_id' => $caja->id,
            'tipo' => $request->tipo,
            'monto' => $request->monto,
            'descripcion' => $request->descripcion,
        ]);

        return back()->with('success', 'Movimiento registrado.');
    }
}
