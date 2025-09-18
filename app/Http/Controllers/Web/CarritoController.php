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
    //METODO OBTENER EL CARRITO CUANDO SE RECARGUE LA PAGINA QUE PERSISTA
    public function obtenerCarrito(){
        $items_carrito = Session::get('items_carrito', []);

        if (empty($items_carrito)) {
            return response()->json([
                'success' => true,
                'carrito' => [],
                'total' => 0,
            ]);
        }

        // Generar estructura con stock e imagen (igual que en agregar)
        $carritoConInfo = collect($items_carrito)->map(function ($item) {
            $prod = \App\Models\Producto::find($item['id']);
            return [
                'id' => $item['id'],
                'nombre' => $item['nombre'],
                'cantidad' => $item['cantidad'],
                'precio' => $item['precio'],
                'stock' => $prod->cantidad ?? 0,
                'imagen' => $prod->imagen ? asset('storage/' . $prod->imagen->ruta) : asset('images/placeholder-caja.png'),
            ];
        });

        $total = $carritoConInfo->sum(fn($item) => $item['precio'] * $item['cantidad']);

        return response()->json([
            'success' => true,
            'carrito' => $carritoConInfo,
            'total' => $total,
        ]);
    }

    //Metodo Agregar ANTES
    /* public function agregar(Request $request, $id)
    {

        return DB::transaction(function () use ($id) {
            $producto = Producto::findOrFail($id);

            if (!$producto) {
                //return Redirect::route('venta.index')->with('error', 'Producto no encontrado.');
                return response()->json(['error' => 'Producto no encontrado.'], 404);
            }

            if ($producto->cantidad < 1) {
                //return Redirect::route('venta.index')->with('error', 'Producto sin stock disponible.');
                return response()->json(['error' => 'Producto sin stock disponible.'], 400);
            }

            $items_carrito = Session::get('items_carrito', []);
            $productoAgregado = false;

            //Obtener los productos ya almacenados
            foreach ($items_carrito as $index => $item) {
                if ($item['id'] == $id) {
                    if ($item['cantidad'] >= $producto->cantidad) {
                        //return Redirect::route('venta.index')->with('error', 'No hay stock suficiente para agregar más unidades.');
                        return response()->json(['error' => 'No hay stock suficiente para agregar más unidades.'], 400);
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

            // 🔁 Generar nueva estructura para respuesta con stock e imagen
            $carritoConInfo = collect($items_carrito)->map(function ($item) {
                $prod = \App\Models\Producto::find($item['id']);
                return [
                    'id' => $item['id'],
                    'nombre' => $item['nombre'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                    'stock' => $prod->cantidad ?? 0,
                    'imagen' => $prod->imagen ? asset('storage/' . $prod->imagen->ruta) : asset('images/placeholder-caja.png'),
                ];
            });

            $total = $carritoConInfo->sum(fn($item) => $item['precio'] * $item['cantidad']);

            //return Redirect::route('venta.index')->with('success', 'Producto agregado correctamente.');
            return response()->json([
                'success' => true,
                'message' => 'Producto agregado correctamente.',
                'carrito' => $carritoConInfo,
                'total' => $total,
            ]);
        });
    } */

    //Metodo Agregar Antes opcion 2
    /* public function agregar(Request $request, $id){

        return DB::transaction(function () use ($id, $request) {
            $producto = Producto::findOrFail($id);

            if (!$producto) {
                return response()->json(['error' => 'Producto no encontrado.'], 404);
            }

            if ($producto->cantidad < 1) {
                return response()->json(['error' => 'Producto sin stock disponible.'], 400);
            }

            $items_carrito = Session::get('items_carrito', []);
            $productoAgregado = false;
            $cantidadAgregada = 1; // por default 1

            // Si el frontend manda cantidad explícita
            if ($request->has('cantidad') && $request->input('cantidad') > 0) {
                $cantidadAgregada = (int) $request->input('cantidad');
            }

            foreach ($items_carrito as $index => $item) {
                if ($item['id'] == $id) {
                    if ($item['cantidad'] + $cantidadAgregada > $producto->cantidad) {
                        return response()->json(['error' => 'No hay stock suficiente para agregar más unidades.'], 400);
                    }

                    $items_carrito[$index]['cantidad'] += $cantidadAgregada;

                    // recalcular precio aplicado según nueva cantidad
                    $items_carrito[$index]['precio'] = $this->getPrecioAplicado($producto, $items_carrito[$index]['cantidad']);
                    $productoAgregado = true;
                    break;
                }
            }

            if (!$productoAgregado) {
                $precioAplicado = $this->getPrecioAplicado($producto, $cantidadAgregada);

                $items_carrito[] = [
                    'id' => $producto->id,
                    'codigo' => $producto->codigo,
                    'nombre' => $producto->nombre,
                    'cantidad' => $cantidadAgregada,
                    'precio' => $precioAplicado,
                ];
            }

            Session::put('items_carrito', $items_carrito);

            // 🔁 Generar respuesta con info adicional
            $carritoConInfo = collect($items_carrito)->map(function ($item) {
                $prod = \App\Models\Producto::find($item['id']);
                return [
                    'id' => $item['id'],
                    'nombre' => $item['nombre'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                    'stock' => $prod->cantidad ?? 0,
                    'imagen' => $prod->imagen ? asset('storage/' . $prod->imagen->ruta) : asset('images/placeholder-caja.png'),
                ];
            });

            $total = $carritoConInfo->sum(fn($item) => $item['precio'] * $item['cantidad']);

            return response()->json([
                'success' => true,
                'message' => 'Producto agregado correctamente.',
                'carrito' => $carritoConInfo,
                'total' => $total,
            ]);
        });
    } */

    //metodo agregar actual
    public function agregar(Request $request, $id){
        return DB::transaction(function () use ($id, $request) {
            $producto = Producto::findOrFail($id);

            if (!$producto) {
                return response()->json(['error' => 'Producto no encontrado.'], 404);
            }

            if ($producto->cantidad < 1) {
                return response()->json(['error' => 'Producto sin stock disponible.'], 400);
            }

            $items_carrito = Session::get('items_carrito', []);
            $productoAgregado = false;
            $cantidadAgregada = $request->input('cantidad', 1);

            foreach ($items_carrito as $index => $item) {
                if ($item['id'] == $id) {
                    if ($item['cantidad'] + $cantidadAgregada > $producto->cantidad) {
                        return response()->json(['error' => 'No hay stock suficiente para agregar más unidades.'], 400);
                    }

                    $items_carrito[$index]['cantidad'] += $cantidadAgregada;

                    // recalcular precio y tipo según nueva cantidad
                    $precioData = $this->getPrecioAplicado($producto, $items_carrito[$index]['cantidad']);
                    $items_carrito[$index]['precio'] = $precioData['precio'];
                    $items_carrito[$index]['tipo_precio'] = $precioData['tipo'];

                    $productoAgregado = true;
                    break;
                }
            }

            if (!$productoAgregado) {
                $precioData = $this->getPrecioAplicado($producto, $cantidadAgregada);

                $items_carrito[] = [
                    'id'          => $producto->id,
                    'codigo'      => $producto->codigo,
                    'nombre'      => $producto->nombre,
                    'cantidad'    => $cantidadAgregada,
                    'precio'      => $precioData['precio'],
                    'tipo_precio' => $precioData['tipo'],
                ];
            }

            Session::put('items_carrito', $items_carrito);

            // 🔁 Generar respuesta con info adicional
            $carritoConInfo = collect($items_carrito)->map(function ($item) {
                $prod = \App\Models\Producto::find($item['id']);
                return [
                    'id'          => $item['id'],
                    'nombre'      => $item['nombre'],
                    'cantidad'    => $item['cantidad'],
                    'precio'      => $item['precio'],
                    'tipo_precio' => $item['tipo_precio'], // se refleja también en respuesta
                    'stock'       => $prod->cantidad ?? 0,
                    'imagen'      => $prod->imagen ? asset('storage/' . $prod->imagen->ruta) : asset('images/placeholder-caja.png'),
                ];
            });

            $total = $carritoConInfo->sum(fn($item) => $item['precio'] * $item['cantidad']);

            return response()->json([
                'success' => true,
                'message' => 'Producto agregado correctamente.',
                'carrito' => $carritoConInfo,
                'total'   => $total,
            ]);
        });
    }


    /**
    *Determina el precio correcto (normal, oferta o mayoreo)
    */
    /* private function getPrecioAplicado($producto, $cantidad = 1){

        $precio = $producto->precio_venta;

        // Oferta vigente
        if (
            $producto->en_oferta &&
            $producto->precio_oferta > 0 &&
            $producto->fecha_inicio_oferta &&
            $producto->fecha_fin_oferta &&
            now()->between($producto->fecha_inicio_oferta, $producto->fecha_fin_oferta)
        ) {
            $precio = $producto->precio_oferta;
        }

        // Mayoreo (si cumple cantidad mínima)
        if (
            $producto->permite_mayoreo &&
            $producto->precio_mayoreo > 0 &&
            $cantidad >= $producto->cantidad_minima_mayoreo
        ) {
            $precio = $producto->precio_mayoreo;
        }

        return $precio;
    } */

    private function getPrecioAplicado($producto, $cantidad){
        // Oferta vigente
        if ($producto->en_oferta && $producto->fecha_fin_oferta >= now()->toDateString()) {
            return [
                'precio' => $producto->precio_oferta,
                'tipo'   => 'oferta'
            ];
        }

        // Mayoreo (si no aplica oferta)
        if ($producto->precio_mayoreo && $cantidad >= $producto->cantidad_mayoreo) {
            return [
                'precio' => $producto->precio_mayoreo,
                'tipo'   => 'mayoreo'
            ];
        }

        // Base (por defecto)
        return [
            'precio' => $producto->precio_venta,
            'tipo'   => 'base'
        ];
    }


    //Metodo Vaciar el Carrito
    public function borrar_carrito()
    {

        // Verificar si el carrito existe y tiene productos
        $items_carrito = Session::get('items_carrito');

        if(empty($items_carrito)) {
            //return redirect()->route('venta.index')->with('error', 'El carrito está vacío, no hay productos que eliminar.');
            return response()->json(['error' => 'El carrito ya está vacío.', ], 400);
        }

        // Si hay productos, los elimina
        if (Session::has('items_carrito')) {
            Session::forget('items_carrito');//elimina la variable de sesión que contiene los productos del carrito.
        }

        //return redirect()->route('venta.index')->with('success', 'Productos eliminados del carrito');
        return response()->json([
            'success' => true,
            'message' => 'Productos eliminados del carrito.',
            'carrito' => [],
            'total' => 0,
        ]);

    }


    //Metodo para quitar un producto del carrito
    public function quitar_carrito($id_producto) {

        $items_carrito = Session::get('items_carrito', []);

        if (!is_array($items_carrito)) {
            $items_carrito = [];
        }

        $productoEncontrado = false; // 👈 IMPORTANTE

        foreach ($items_carrito as $key => $carrito) {
            if ($carrito['id'] == $id_producto) {
                $productoEncontrado = true;

                if ($carrito['cantidad'] > 1) {
                    $items_carrito[$key]['cantidad'] -= 1;
                } else {
                    unset($items_carrito[$key]);
                    $items_carrito = array_values($items_carrito);
                }

                break;
            }
        }

        if (!$productoEncontrado) {
            return response()->json(['error' => 'Producto no encontrado en el carrito.'], 404);
        }

        Session::put('items_carrito', $items_carrito);

        $carritoConInfo = collect($items_carrito)->map(function ($item) {
            $prod = \App\Models\Producto::find($item['id']);
            return [
                'id' => $item['id'],
                'nombre' => $item['nombre'],
                'cantidad' => $item['cantidad'],
                'precio' => $item['precio'],
                'stock' => $prod?->cantidad ?? 0,
                'imagen' => $prod && $prod->imagen ? asset('storage/' . $prod->imagen->ruta) : asset('images/placeholder-caja.png'),
            ];
        });

        $total = $carritoConInfo->sum(fn($item) => $item['precio'] * $item['cantidad']);

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del carrito.',
            'carrito' => $carritoConInfo,
            'total' => $total,
        ]);

    }


    public function update(Request $request, $id){//$id_producto

        $nuevaCantidad = max(1, (int)$request->input('cantidad'));

        return DB::transaction(function () use ($id, $nuevaCantidad) {

            $producto = Producto::findOrFail($id);

            if ($producto->cantidad < $nuevaCantidad) {
                return response()->json(['error' => 'No hay stock suficiente para esa cantidad.'], 400);
            }

            $items_carrito = Session::get('items_carrito', []);

            foreach ($items_carrito as $index => $item) {
                if ($item['id'] == $id) {
                    $items_carrito[$index]['cantidad'] = $nuevaCantidad;
                    Session::put('items_carrito', $items_carrito);

                    // Preparar respuesta actualizada
                    $carritoConInfo = collect($items_carrito)->map(function ($item) {
                        $prod = \App\Models\Producto::find($item['id']);
                        return [
                            'id' => $item['id'],
                            'nombre' => $item['nombre'],
                            'cantidad' => $item['cantidad'],
                            'precio' => $item['precio'],
                            'stock' => $prod->cantidad ?? 0,
                            'imagen' => $prod->imagen ? asset('storage/' . $prod->imagen->ruta) : asset('images/placeholder-caja.png'),
                        ];
                    });

                    $total = $carritoConInfo->sum(fn($item) => $item['precio'] * $item['cantidad']);

                    return response()->json([
                        'success' => true,
                        'message' => 'Cantidad actualizada correctamente.',
                        'carrito' => $carritoConInfo,
                        'total' => $total,
                    ]);
                }
            }

            return response()->json(['error' => 'El producto no se encuentra en el carrito.'], 404);
        });
    }

}
