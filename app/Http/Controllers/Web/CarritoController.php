<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\DetalleVenta;
use App\Models\Pago;
use Illuminate\Support\Facades\Auth;

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

        // Verificar si el carrito existe y tiene productos
        $items_carrito = Session::get('items_carrito');
        if(empty($items_carrito)) {
            return redirect()->route('venta.index')->with('error', 'El carrito está vacío, no hay productos que eliminar.');
        }

        // Si hay productos, los elimina
        if (Session::has('items_carrito')) {
            Session::forget('items_carrito');//elimina la variable de sesión que contiene los productos del carrito.
        }

        return redirect()->route('venta.index')->with('success', 'Productos eliminados del carrito');

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


    public function update(Request $request, $id){//$id_producto

        $nuevaCantidad = max(1, (int)$request->input('cantidad'));

        return DB::transaction(function () use ($id, $nuevaCantidad) {

            $producto = Producto::findOrFail($id);

            if ($producto->cantidad < $nuevaCantidad) {
                return redirect()->route('venta.index')
                    ->with('error', 'No hay stock suficiente para esa cantidad.');
            }

            $items_carrito = Session::get('items_carrito', []);

            foreach ($items_carrito as $index => $item) {
                if ($item['id'] == $id) {
                    $items_carrito[$index]['cantidad'] = $nuevaCantidad;
                    Session::put('items_carrito', $items_carrito);

                    return redirect()->route('venta.index')
                        ->with('success', 'Cantidad actualizada correctamente.');
                }
            }

            // Si el producto no está en el carrito, opcionalmente podrías agregarlo
            return redirect()->route('venta.index')
                ->with('error', 'El producto no se encuentra en el carrito.');
        });
    }

}
