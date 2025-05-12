<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use App\Models\DetalleVenta;
use App\Models\Producto;
use Pdf;

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

    public function vista_detalle($id){

        $venta = Venta::select(
            'ventas.*',
            'users.name as nombre_usuario'
        )
        ->join('users', 'ventas.user_id', '=', 'users.id')
        ->where('ventas.id', $id)
        ->firstOrFail();

        $detalles = DetalleVenta::select(
            'detalle_venta.*',
            'productos.nombre as nombre_producto'
        )
        ->join('productos', 'detalle_venta.producto_id', '=', 'productos.id')
        ->where('venta_id', $id)
        ->get();

        return view('modulos.detalleventas.detalle_venta', compact('venta', 'detalles'));
    }

    public function revocar($id) {
        DB::beginTransaction();
        try {

            $detalles = DetalleVenta::select(
                'producto_id', 'cantidad'
            )
            ->where('venta_id', $id)
            ->get();

            //devolver stock
            foreach($detalles as $detalle) {
                Producto::where('id', $detalle->producto_id)
                ->increment('cantidad', $detalle->cantidad);
            }

            //eliminar productos vendidos y la venta
            DetalleVenta::where('venta_id', $id)->delete();
            Venta::where('id', $id)->delete();

            DB::commit();
            return to_route('detalleventas.index')->with('success', 'Eliminacion de venta exitosa!!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return to_route('detalleventas.index')->with('error', 'No se pudo Eliminar la venta!!');
        }
    }

    public function generarTicket($id){

        $venta = Venta::select(
            'ventas.*',
            'users.name as nombre_usuario'
        )
        ->join('users', 'ventas.user_id', '=', 'users.id')
        ->where('ventas.id', $id)
        ->firstOrFail();

        $detalles = DetalleVenta::select(
            'detalle_venta.*',
            'productos.nombre as nombre_producto'
        )
        ->join('productos', 'detalle_venta.producto_id', '=', 'productos.id')
        ->where('venta_id', $id)
        ->get();

        // Generar PDF con tamaño personalizado tipo ticket (80mm x altura ajustable)
        $pdf = Pdf::loadView('modulos.detalleventas.ticket', compact('venta', 'detalles'))
                ->setPaper([0, 0, 300, 900], 'portrait'); // 80mm de ancho

        return $pdf->stream("ticket_compra_{$venta->id}.pdf");
    }

}
