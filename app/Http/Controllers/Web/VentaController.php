<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Models\Pago;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Categoria;

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
            //crear la venta
            $venta = new Venta();
            $venta->user_id = Auth::id(); // ← asignas aquí el usuario
            $venta->cliente_id  = $request->cliente_id;   // ← asignas aquí el cliente
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

            Session::forget('items_carrito');
            DB::commit();
            return to_route('venta.index')->with('folio_generado', $venta->folio);;
        } catch (\Throwable $th) {
            DB::rollBack();
            return to_route('venta.index')->with('error', 'Error al procesar la venta!!' . $th->getMessage());
        }
    }


}
