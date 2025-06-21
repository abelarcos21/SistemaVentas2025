<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pago;
use App\Models\Venta;

class PagoController extends Controller
{
    //
    public function store(){

        $request->validate([
            'venta_id' => 'required|exists:ventas,id',//que exista la venta_id en la tabla ventas
            'metodo_pago.*' => 'required|string',
            'monto.*' => 'required|numeric|min:0.01',
        ]);

        $venta_id = $request->venta_id;

        foreach($request->metodo_pago as $index => $metodo){
            Pago::create([
                'venta_id' => $venta_id,
                'metodo_pago' => $metodo,
                'monto' => $request->monto[$index],
            ]);
        }

        return redirect()->back()->with('success', 'Pagos registrados correctamente.');
    }

}
