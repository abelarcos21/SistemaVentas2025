<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\DetalleVenta;

use Auth;

class CarritoController extends Controller
{
    //Metodo Agregar
    public function agregar($id)
    {

        return DB::transaction(function () use ($id) {
            $producto = Producto::findOrFail($id);

            if (!$producto) {
                return Redirect::route('venta.index')->with('error', 'Producto no encontrado.');
            }

            if ($producto->cantidad < 1) {
                return Redirect::route('venta.index')->with('error', 'Producto sin stock disponible.');
            }

            $items_carrito = Session::get('items_carrito', []);
            $productoAgregado = false;

            //Obtener los productos ya almacenados
            foreach ($items_carrito as $index => $item) {
                if ($item['id'] == $id) {
                    if ($item['cantidad'] >= $producto->cantidad) {
                        return Redirect::route('venta.index')->with('error', 'No hay stock suficiente para agregar más unidades.');
                    }

                    $items_carrito[$index]['cantidad'] += 1;
                    $productoAgregado = true;
                    break;
                }
            }

            //agregar el nuevo producto
            if (!$productoAgregado) {
                $items_carrito[] = [
                    'id' => $producto->id,
                    'codigo' => $producto->codigo,
                    'nombre' => $producto->nombre,
                    'cantidad' => 1,
                    'precio' => $producto->precio_venta,
                ];
            }

            //realmente creamos una sesion
            Session::put('items_carrito', $items_carrito);

            return Redirect::route('venta.index')->with('success', 'Producto agregado correctamente.');
        });
    }

    //Metodo Vaciar el Carrito
    public function borrar_carrito()
    {

        Session::forget('items_carrito');

        return redirect()->route('venta.index')->with('success', 'Producto eliminado del carrito');

    }


    //Metodo para quitar un producto del carrito
    public function quitar_carrito($id_producto) {
        $items_carrito = Session::get('items_carrito', []);

        foreach ($items_carrito as $key => $carrito) {
            if ($carrito['id'] == $id_producto) {
                if ($carrito['cantidad'] > 1) {
                    $items_carrito[$key]['cantidad'] -= 1;
                } else {
                    unset($items_carrito[$key]);
                    $items_carrito = array_values($items_carrito); // 👈 Reindexamos el array
                }
                break;
            }
        }
        Session::put('items_carrito', $items_carrito);
        return to_route('venta.index')->with('success', 'Producto actualizado en el carrito.');
    }

    //Metodo para realizar una venta
    public function vender(){
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
            //crear la venta
            $venta = new Venta();
            $venta->user_id = Auth::id();
            $venta->total_venta = $totalVenta;
            $venta->save();

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

                $producto->cantidad -= $item['cantidad'];
                $producto->save();
            }

            Session::forget('items_carrito');
            DB::commit();
            return to_route('venta.index')->with('success', 'Venta realizada con exito!!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return to_route('venta.index')->with('error', 'Error al procesar la venta!!' . $th->getMessage());
        }
    }

}
