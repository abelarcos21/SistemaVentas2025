<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Mail\VentaRealizada;
use Illuminate\Support\Facades\Mail;
use App\Models\Pago;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Categoria;
use App\Models\Caja;

class VentaController extends Controller
{
    //index
    public function index(Request $request){

        $productos = Producto::paginate(12);

        if($request->ajax()){
            return view('modulos.productos.ajaxproductos', compact('productos'))->render();
        }


        $clientes = Cliente::orderBy('nombre')->get();

        /* $categorias = Categoria::withCount(['productos' => function ($query){
            $query->where('cantidad', '>', 0);

        }])->get(); */

        // NO pongas condiciones aquí para que no falle el conteo
        $categorias = Categoria::withCount('productos')->get();

        //$totalProductos = Producto::where('cantidad', '>', 0)->count();
        //Evita poner ->where('cantidad', '>', 0) dentro del withCount, a menos que estés 100%
        //seguro de que todos los productos tienen cantidad válida. Si alguno tiene null o cantidad = 0, no lo cuenta.
        $totalProductos = Producto::count(); // o ->where('cantidad', '>', 0)->count() si quieres filtrar

        return view('modulos.ventas.index', compact('totalProductos', 'productos', 'clientes','categorias'));
    }

    //Metodo para realizar una venta
    public function vender(Request $request){

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'metodo_pago.*' => 'required|string',
            'monto.*' => 'required|numeric|min:0.01',
        ]);

        $items_carrito = Session::get('items_carrito', []);

        //validar si el carrito esta vacio
        if (empty($items_carrito)) {
            return to_route('venta.index')->with('error', 'El carrito esta vacio!!');
        }

        //iniciar la transaccion
        DB::beginTransaction();

        try {
            $totalVenta = 0;
            foreach ($items_carrito as $item) {
                $totalVenta += $item['cantidad'] * $item['precio'];
            }

            // 👉 VALIDAR QUE EL MONTO TOTAL DE PAGOS SEA IGUAL AL TOTAL DE LA VENTA
            $totalPagos = 0;
            foreach ($request->monto as $monto) {
                $totalPagos += $monto;
            }

            // Calcular el cambio
            $cambio = $totalPagos - $totalVenta;


            // Si el pago es insuficiente
            if ($totalPagos < $totalVenta) {
                DB::rollBack();

                // Formatear los montos para mostrar con decimales
                $totalVentaFormateado = number_format($totalVenta, 2);
                $totalPagosFormateado = number_format($totalPagos, 2);
                $diferencia = number_format($totalVenta - $totalPagos, 2);

                return to_route('venta.index')->with('error_pago', [
                    'tipo' => 'insuficiente',
                    'mensaje' => "El monto del pago es insuficiente. Total de la venta: ${totalVentaFormateado}, Total pagado: ${totalPagosFormateado}. Falta: ${diferencia}"
                ]);
            }

            // 1. Buscar la caja abierta del usuario actual
            $caja = Caja::getCajaActivaByUser(auth()->id());

            if (!$caja) {
                DB::rollBack();
                return to_route('venta.index')->with('error', 'No tienes una caja abierta. Abre una antes de registrar ventas.');
            }

            //Crear la venta con la caja asociada
            $venta = new Venta();
            $venta->caja_id = $caja->id; // aquí se liga la venta con la caja
            $venta->user_id = Auth::id(); // ← asignas aquí el usuario
            $venta->cliente_id  = $request->cliente_id;   // ← asignas aquí el cliente
            $venta->empresa_id = 1;
            $venta->total_venta = $totalVenta;
            $venta->estado = 'completada'; // ← Aquí asignas el estado de la venta

            // 👉 generar folio consecutivo Obtener o crear el folio actual con bloqueo
            //Este método evita colisiones de folios porque usa lockForUpdate() dentro de la transacción.
            $folio = \App\Models\Folio::lockForUpdate()->firstOrCreate(
                ['serie' => '001'],
                ['ultimo_numero' => 0]
            );

            // Incrementar y guardar en la bd
            $folio->ultimo_numero += 1;
            $folio->save();

            // Generar el folio formateado y asignamos el folio ala venta
            $venta->folio = sprintf('%s-%06d', $folio->serie, $folio->ultimo_numero);

            // Actualizar el total de ventas en la caja
            $caja->increment('total_ventas', $venta->total_venta);

            $venta->save();//guardamos la venta

            foreach ($items_carrito as $item) {
                $producto = Producto::find($item['id']);
                //verificar si tenemos suficiente stock
                if ($producto->cantidad < $item['cantidad']) {
                    DB::rollBack();
                    return to_route('venta.index')->with('error', 'No hay stock suficiente para ' . $producto->nombre);
                }

                $detalle = new DetalleVenta();
                $detalle->venta_id = $venta->id;
                $detalle->producto_id = $item['id'];
                $detalle->cantidad = $item['cantidad'];
                $detalle->precio_unitario = $item['precio'];
                $detalle->sub_total = $item['cantidad'] * $item['precio'];
                $detalle->save();

                $producto->cantidad -= $item['cantidad']; //le resta el producto cantidad disminuye al vender
                $producto->save();
            }

            // Guardar los pagos
            foreach ($request->metodo_pago as $i => $metodo) {
                Pago::create([
                    'venta_id' => $venta->id,
                    'metodo_pago' => $metodo,
                    'monto' => $request->monto[$i],
                ]);
            }


            if($request->has('enviar_correo') && $venta->cliente && $venta->cliente->correo) {
                $venta->load(['cliente', 'detalles.producto']); // Carga relaciones si aún no están
                Mail::to($venta->cliente->correo)->send(new VentaRealizada($venta));
            }

            Session::forget('items_carrito');

            DB::commit();

            // Si hay cambio, enviarlo en la sesión las variables
            if ($cambio > 0) {
                return to_route('venta.index')->with([
                    'folio_generado' => $venta->folio,
                    'venta_id' => $venta->id, //para manejar los botones de ticket/boletapdf en la vista de vender al generar una venta
                    'cambio' => $cambio,
                    'total_venta' => $totalVenta,
                    'total_pagado' => $totalPagos
                ]);
            } else {
                return to_route('venta.index')->with([

                    'folio_generado' => $venta->folio,
                    'venta_id' => $venta->id, //para manejar los botones de ticket/boletapdf en la vista de vender al generar una venta sin cambio
                ]);
            }
            return to_route('venta.index')->with('folio_generado', $venta->folio);;
        } catch (\Throwable $th) {
            DB::rollBack();
            return to_route('venta.index')->with('error', 'Error al procesar la venta!!' . $th->getMessage());
        }
    }


}
