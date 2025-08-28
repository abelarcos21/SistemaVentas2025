<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    //

    public function index(){
        $cajas = Caja::with('usuario')->latest()->get();
        return view('modulos.cajas.index', compact('cajas'));
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
            'descripcion' => 'nullable|string',
        ]);

        /* dd([
            'caja_id' => $caja->id,
            'tipo' => $request->tipo,
            'monto' => $request->monto,
            'descripcion' => $request->descripcion,
        ]); */

        MovimientoCaja::create([
            'caja_id' => $caja->id,
            'tipo' => $request->tipo,
            'monto' => $request->monto,
            'descripcion' => $request->descripcion,
        ]);


        if ($request->tipo == 'ingreso') {
            $caja->increment('total_ingresos', $request->monto);
        } else {
            $caja->increment('total_egresos', $request->monto);
        }

        return back()->with('success', 'Movimiento registrado.');
    }
}
