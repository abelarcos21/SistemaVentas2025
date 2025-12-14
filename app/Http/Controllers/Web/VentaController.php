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
use App\Models\Marca;
use App\Models\Caja;

class VentaController extends Controller
{
    //index
    public function index(Request $request){

        $productos = Producto::with('moneda')->paginate(10);//trae relacion de moneda para cada producto

        if($request->ajax()){
            return view('modulos.productos.ajaxproductos', compact('productos'))->render();
        }

        $clientes = Cliente::orderBy('nombre')->get();

        // NO pongas condiciones aquí para que no falle el conteo
        // Paginar 10 categorías por página y usar un nombre de parámetro distinto
        // para no interferir con otras paginaciones en la misma vista
        $categorias = Categoria::withCount('productos')
                        ->orderBy('nombre')
                        ->paginate(10, ['*'], 'cat_page');

        $marcas = Marca::withCount('productos')->get();

        //$totalProductos = Producto::where('cantidad', '>', 0)->count();
        //Evita poner ->where('cantidad', '>', 0) dentro del withCount, a menos que estés 100%
        //seguro de que todos los productos tienen cantidad válida. Si alguno tiene null o cantidad = 0, no lo cuenta.
        $totalProductos = Producto::count(); // o ->where('cantidad', '>', 0)->count() si quieres filtrar

        return view('modulos.ventas.index', compact('totalProductos', 'productos', 'clientes','categorias','marcas'));
    }

    public function categorias(Request $request){
        // Contar total de productos (para el botón "Todas las categorías")
        $totalProductos = Producto::count();

        // Paginamos las categorías con su conteo de productos
        $categorias = Categoria::withCount('productos')
            ->orderBy('nombre')
            ->paginate(10, ['*'], 'cat_page'); // <- clave importante

        // Si es AJAX devolvemos solo el HTML parcial de categorías
        if ($request->ajax()) {
            return view('modulos.categorias.partials.categorias', compact('categorias', 'totalProductos'))->render();
        }

        // Si es carga normal, enviamos a la vista principal
        return view('modulos.ventas.index', compact('categorias', 'totalProductos'));
    }

    //Metodo para realizar una venta
    public function vender(Request $request){
        // Validamos los nuevos campos (nullable porque no siempre vienen)
        // VALIDACIÓN ADAPTADA AL NUEVO FORMULARIO
        // Ahora esperamos strings y números simples, no arrays.
        $request->validate([
            'cliente_id'    => 'required|exists:clientes,id',
            'metodo_pago'   => 'required|string', // Antes era array
            'pago_recibido' => 'required|numeric|min:0',
            'referencia' => 'nullable|string',
            'monto_efectivo' => 'nullable|numeric',
            'monto_tarjeta' => 'nullable|numeric',
            'nota_venta'    => 'nullable|string',
        ]);

        $items_carrito = Session::get('items_carrito', []);

        if (empty($items_carrito)) {
            return to_route('venta.index')->with('error', 'El carrito está vacío.');
        }

        DB::beginTransaction();

        try {
            // --- A. CALCULAR TOTAL DE LA VENTA ---
            $totalVenta = 0;

            // Primero calculamos el total real del carrito
            foreach ($items_carrito as $item) {
                $producto = Producto::lockForUpdate()->find($item['id']); // Bloqueamos fila para evitar condiciones de carrera

                // Lógica de Precios (Oferta vs Mayoreo vs Base)
                $precioAplicado = $producto->precio_venta;
                $tipoPrecio = 'base';

                if ($producto->en_oferta && $producto->fecha_fin_oferta >= now()) {
                    $precioAplicado = $producto->precio_oferta;
                    $tipoPrecio = 'oferta';
                } elseif ($producto->precio_mayoreo && $item['cantidad'] >= $producto->cantidad_minima_mayoreo) {
                    $precioAplicado = $producto->precio_mayoreo;
                    $tipoPrecio = 'mayoreo';
                }

                // Guardamos el precio calculado en el array temporal para no recalcular abajo
                $item['precio_calculado'] = $precioAplicado;
                $item['tipo_precio_calculado'] = $tipoPrecio;

                $totalVenta += $item['cantidad'] * $precioAplicado;
            }

            // --- B. VALIDAR PAGOS ---
            $pagoRecibido = $request->pago_recibido;
            $metodoPago = $request->metodo_pago;

            // Si es tarjeta, asumimos que se cobró el total exacto (o lo que venga del input)
            // Si es efectivo, validamos que alcance
            if ($metodoPago === 'efectivo' && $pagoRecibido < $totalVenta) {
                DB::rollBack();
                return to_route('venta.index')->with('error', 'El pago es insuficiente. Faltan: $' . number_format($totalVenta - $pagoRecibido, 2));
            }

            // --- C. OBTENER CAJA Y FOLIO ---
            $caja = Caja::getCajaActivaByUser(auth()->id());
            if (!$caja) {
                DB::rollBack();
                return to_route('venta.index')->with('error', 'No tienes caja abierta.');
            }

            $folio = \App\Models\Folio::lockForUpdate()->firstOrCreate(
                ['serie' => '001'], ['ultimo_numero' => 0]
            );
            $folio->ultimo_numero += 1;
            $folio->save();

            // --- D. GUARDAR VENTA ---
            $venta = new Venta();
            $venta->caja_id = $caja->id;
            $venta->user_id = Auth::id();
            $venta->cliente_id  = $request->cliente_id;
            $venta->empresa_id = 1;
            $venta->total_venta = $totalVenta;
            $venta->estado = 'completada';
            $venta->folio = sprintf('%s-%06d', $folio->serie, $folio->ultimo_numero);
            $venta->nota_venta = $request->nota_venta; // Guardamos la nota del modal
            $venta->save();

            // Actualizar caja
            $caja->increment('total_ventas', $totalVenta);

            // --- E. GUARDAR DETALLES Y DESCONTAR STOCK ---
            foreach ($items_carrito as $item) {
                $producto = Producto::find($item['id']);

                // Re-verificar stock
                if ($producto->cantidad < $item['cantidad']) {
                    DB::rollBack();
                    return to_route('venta.index')->with('error', 'Stock insuficiente para ' . $producto->nombre);
                }

                // Crear detalle
                $detalle = new DetalleVenta();
                $detalle->venta_id = $venta->id;
                $detalle->producto_id = $item['id'];
                $detalle->cantidad = $item['cantidad'];

                // Usamos los precios que calculamos arriba (recalculados por seguridad)
                $precioFinal = $producto->precio_venta;
                $tipoFinal = 'base';
                if ($producto->en_oferta && $producto->fecha_fin_oferta >= now()) {
                    $precioFinal = $producto->precio_oferta;
                    $tipoFinal = 'oferta';
                } elseif ($producto->precio_mayoreo && $item['cantidad'] >= $producto->cantidad_minima_mayoreo) {
                    $precioFinal = $producto->precio_mayoreo;
                    $tipoFinal = 'mayoreo';
                }

                $detalle->precio_unitario_aplicado = $precioFinal;
                $detalle->sub_total = $item['cantidad'] * $precioFinal;
                $detalle->tipo_precio_aplicado = $tipoFinal;
                $detalle->save();

                // Descontar inventario
                $producto->decrement('cantidad', $item['cantidad']);
            }

            // --- F. GUARDAR PAGO EFECTIVO/TARJETA/TRANSFERENCIA O MIXTO(EFECTIVO/TARJETA) ---
            if ($request->metodo_pago === 'mixto') {
                // Guardamos DOS pagos
                if ($request->monto_efectivo > 0) {
                    Pago::create([
                        'venta_id' => $venta->id,
                        'metodo_pago' => 'efectivo',
                        'monto' => $request->monto_efectivo,
                        'referencia' => null
                    ]);
                }
                if ($request->monto_tarjeta > 0) {
                    Pago::create([
                        'venta_id' => $venta->id,
                        'metodo_pago' => 'tarjeta',
                        'monto' => $request->monto_tarjeta,
                        'referencia' => $request->referencia // La referencia va a la parte de tarjeta
                    ]);
                }
            } else {
                // Pago Único (Efectivo, Tarjeta o Transferencia)
                Pago::create([
                    'venta_id' => $venta->id,
                    'metodo_pago' => $request->metodo_pago,
                    'monto' => $request->pago_recibido, // En efectivo es lo que dio, en otros es el total
                    'referencia' => $request->referencia,
                    'cambio' => ($request->metodo_pago === 'efectivo') ? ($request->pago_recibido - $totalVenta) : 0
                ]);
            }

            // --- G. CORREO Y CIERRE ---
            if($request->has('enviar_correo') && $venta->cliente && $venta->cliente->correo) {
                // Envío en segundo plano (queue) es recomendable, si no, directo:
                try {
                    Mail::to($venta->cliente->correo)->send(new VentaRealizada($venta));
                } catch (\Exception $e) {
                    // Loguear error pero no detener la venta
                    \Log::error('Error enviando correo: ' . $e->getMessage());
                }
            }

            Session::forget('items_carrito');
            DB::commit();

            // Calculamos cambio para mostrar en el frontend
            $cambio = ($metodoPago === 'efectivo') ? ($pagoRecibido - $totalVenta) : 0;

            // Retornar éxito
            return to_route('venta.index')->with([
                'folio_generado' => $venta->folio,
                'venta_id' => $venta->id,
                'cambio' => $cambio,
                'total_venta' => $totalVenta,
                'total_pagado' => $pagoRecibido
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            // Loguear el error real para que lo veas en laravel.log
            \Log::error($th);
            return to_route('venta.index')->with('error', 'Error del sistema: ' . $th->getMessage());
        }
    }

    /* public function vender(Request $request){ */

       /*  $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'metodo_pago' => 'required|array|min:1',
            'metodo_pago.*' => 'required|string',
        ]);

        $items_carrito = Session::get('items_carrito', []); */

        //validar si el carrito esta vacio
        /* if (empty($items_carrito)) {
            return to_route('venta.index')->with('error', 'El carrito esta vacio!!');
        } */

        //iniciar la transaccion
        /* DB::beginTransaction(); */

        /* try { */
           /*  $totalVenta = 0;
            foreach ($items_carrito as $item) {

                $producto = Producto::find($item['id']);

                $precioBase = $producto->precio_venta;
                $tipoPrecio = 'base';

                //Oferta vigente tiene prioridad
                if ($producto->en_oferta && $producto->fecha_fin_oferta >= now()) {
                    $precioBase = $producto->precio_oferta;
                    $tipoPrecio = 'oferta';
                }elseif($producto->precio_mayoreo && $item['cantidad'] >= $producto->cantidad_minima_mayoreo){//Mayoreo solo si NO hay oferta
                    $precioBase = $producto->precio_mayoreo;
                    $tipoPrecio = 'mayoreo';
                }

                $totalVenta += $item['cantidad'] * $precioBase;
            } */

            // CALCULAR TOTAL DE PAGOS (incluye mixto) SEA IGUAL AL TOTAL DE LA VENTA
           /*  $totalPagos = 0;
            $contadorMixto = 0;
            $contadorNormal = 0; */


           /*  foreach ($request->metodo_pago as $metodo) {
                if ($metodo === 'mixto') {
                    $efectivo = $request->monto_efectivo[$contadorMixto] ?? 0;
                    $tarjeta = $request->monto_tarjeta[$contadorMixto] ?? 0;

                    // Validar que ambos montos existan y sean mayores a 0
                    if ($efectivo <= 0 || $tarjeta <= 0) {
                        DB::rollBack();
                        return to_route('venta.index')->with('error', 'En pago mixto, tanto el efectivo como la tarjeta deben ser mayores a 0');
                    }

                    $totalPagos += $efectivo + $tarjeta;
                    $contadorMixto++;
                } else {
                    $monto = $request->monto[$contadorNormal] ?? 0;

                    if ($monto <= 0) {
                        DB::rollBack();
                        return to_route('venta.index')->with('error', 'El monto del pago debe ser mayor a 0');
                    }

                    $totalPagos += $monto;
                    $contadorNormal++;
                }
            } */

            // Calcular el cambio
            /* $cambio = $totalPagos - $totalVenta; */


            // Si el pago es insuficiente
           /*  if ($totalPagos < $totalVenta) {
                DB::rollBack();

                // Formatear los montos para mostrar con decimales
                $totalVentaFormateado = number_format($totalVenta, 2);
                $totalPagosFormateado = number_format($totalPagos, 2);
                $diferencia = number_format($totalVenta - $totalPagos, 2);

                return to_route('venta.index')->with('error_pago', [
                    'tipo' => 'insuficiente',
                    'mensaje' => "El monto del pago es insuficiente. Total de la venta: ${totalVentaFormateado}, Total pagado: ${totalPagosFormateado}. Falta: ${diferencia}"
                ]);
            } */

            // 1. Buscar la caja abierta del usuario actual
           /*  $caja = Caja::getCajaActivaByUser(auth()->id());

            if (!$caja) {
                DB::rollBack();
                return to_route('venta.index')->with('error', 'No tienes una caja abierta. Abre una antes de registrar ventas.');
            } */

            //Crear la venta con la caja asociada
           /*  $venta = new Venta();
            $venta->caja_id = $caja->id; // aquí se liga la venta con la caja
            $venta->user_id = Auth::id(); // ← asignas aquí el usuario
            $venta->cliente_id  = $request->cliente_id;   // ← asignas aquí el cliente
            $venta->empresa_id = 1;
            $venta->total_venta = $totalVenta;
            $venta->estado = 'completada'; */ // ← Aquí asignas el estado de la venta

            //generar folio consecutivo Obtener o crear el folio actual con bloqueo
            //Este método evita colisiones de folios porque usa lockForUpdate() dentro de la transacción.
            /* $folio = \App\Models\Folio::lockForUpdate()->firstOrCreate(
                ['serie' => '001'],
                ['ultimo_numero' => 0]
            ); */

            // Incrementar y guardar en la bd
            /* $folio->ultimo_numero += 1;
            $folio->save();
 */
            // Generar el folio formateado y asignamos el folio ala venta
           /*  $venta->folio = sprintf('%s-%06d', $folio->serie, $folio->ultimo_numero); */

            // Actualizar el total de ventas en la caja
            /* $caja->increment('total_ventas', $venta->total_venta); */

           /*  $venta->save(); *///guardamos la venta

            /* foreach ($items_carrito as $item) {
                $producto = Producto::find($item['id']);

                $precioBase = $producto->precio_venta;
                $tipoPrecio = 'base';

                //Oferta vigente tiene prioridad
                if ($producto->en_oferta && $producto->fecha_fin_oferta >= now()) {
                    $precioBase = $producto->precio_oferta;
                    $tipoPrecio = 'oferta';
                }elseif($producto->precio_mayoreo && $item['cantidad'] >= $producto->cantidad_minima_mayoreo){//Mayoreo solo si NO hay oferta
                    $precioBase = $producto->precio_mayoreo;
                    $tipoPrecio = 'mayoreo';
                }

                //verificar si tenemos suficiente stock
                if ($producto->cantidad < $item['cantidad']) {
                    DB::rollBack();
                    return to_route('venta.index')->with('error', 'No hay stock suficiente para ' . $producto->nombre);
                }

                $detalle = new DetalleVenta();
                $detalle->venta_id = $venta->id;
                $detalle->producto_id = $item['id'];
                $detalle->cantidad = $item['cantidad'];
                $detalle->precio_unitario_aplicado = $precioBase;
                $detalle->sub_total = $item['cantidad'] * $precioBase;
                $detalle->tipo_precio_aplicado = $tipoPrecio; //aquí guardamos TIPO PRECIO APLICADO
                $detalle->save();

                $producto->cantidad -= $item['cantidad']; //le resta el producto cantidad disminuye al vender
                $producto->save();
            } */


            // Guardar los pagos
           /*  $contadorMixto = 0;
            $contadorNormal = 0; */

            /* foreach ($request->metodo_pago as $metodo) {
                if ($metodo === 'mixto') {
                    $efectivo = $request->monto_efectivo[$contadorMixto] ?? 0;
                    $tarjeta = $request->monto_tarjeta[$contadorMixto] ?? 0;

                    if ($efectivo > 0) {
                        Pago::create([
                            'venta_id' => $venta->id,
                            'metodo_pago' => 'efectivo',
                            'monto' => $efectivo,
                            'referencia' => null,
                        ]);
                    }

                    if ($tarjeta > 0) {
                        Pago::create([
                            'venta_id' => $venta->id,
                            'metodo_pago' => 'tarjeta',
                            'monto' => $tarjeta,
                            'referencia' => $request->referencia_tarjeta[$contadorMixto] ?? null,
                        ]);
                    }

                    $contadorMixto++;
                } else {
                    $monto = $request->monto[$contadorNormal] ?? 0;

                    if ($monto > 0) {
                        Pago::create([
                            'venta_id' => $venta->id,
                            'metodo_pago' => $metodo,
                            'monto' => $monto,
                            'referencia' => $request->referencia[$contadorNormal] ?? null,
                        ]);
                    }

                    $contadorNormal++;
                }
            } */


           /*  if($request->has('enviar_correo') && $venta->cliente && $venta->cliente->correo) {
                $venta->load(['cliente', 'detalles.producto']); // Carga relaciones si aún no están
                Mail::to($venta->cliente->correo)->send(new VentaRealizada($venta));
            } */

           /*  Session::forget('items_carrito');

            DB::commit(); */

            // Si hay cambio, enviarlo en la sesión las variables
            /* if ($cambio > 0) {
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
            } */
          /*   return to_route('venta.index')->with('folio_generado', $venta->folio);;
        } catch (\Throwable $th) {
            DB::rollBack();
            return to_route('venta.index')->with('error', 'Error al procesar la venta!!' . $th->getMessage());
        } */
   // }


}
