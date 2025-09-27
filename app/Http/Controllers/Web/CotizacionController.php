<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Cotizacion;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\CotizacionDetalle;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Caja;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;


class CotizacionController extends Controller
{

    /**
     * Mostrar listado de cotizaciones
     */
    public function index(Request $request){

        $cotizaciones = Cotizacion::with('cliente')
            ->when($request->cliente_id, fn($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
            ->latest()
            ->paginate(10);

        $clientes = Cliente::all();

        return view('modulos.cotizaciones.index', compact('cotizaciones'));
    }

    /**
     * Formulario de creación de cotización
     */
    public function create(){
        $clientes = Cliente::all();
        $productos = Producto::all();
        return view('modulos.cotizaciones.create', compact('clientes', 'productos'));
    }

    /**
     * Guardar cotización en BD
    */

    /* public function store(Request $request) {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos' => 'required|array|min:1',
            'cantidades' => 'required|array|min:1',
            'precios' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request) {
            // Calcular totales en el servidor
            $subtotal = 0;
            $productosData = [];

            // Preparar los datos de productos y calcular subtotal
            for ($i = 0; $i < count($request->productos); $i++) {
                $cantidad = $request->cantidades[$i];
                $precio = $request->precios[$i];
                $totalProducto = $cantidad * $precio;

                $productosData[] = [
                    'producto_id' => $request->productos[$i],
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'total' => $totalProducto
                ];

                $subtotal += $totalProducto;
            }

            // Calcular impuestos (ejemplo: 16% IVA, ajusta según tu necesidad)
            $impuestos = $subtotal * 0.16;
            $total = $subtotal + $impuestos;

            // Crear la cotización
            $cotizacion = Cotizacion::create([
                'cliente_id' => $request->cliente_id,
                'user_id'    => auth()->id(),
                'fecha'      => now(),
                'subtotal'   => $subtotal,
                'impuestos'  => $impuestos,
                'total'      => $total,
                'estado'     => 'pendiente',
            ]);

            // Crear los detalles
            foreach ($productosData as $producto) {
                CotizacionDetalle::create([
                    'cotizacion_id' => $cotizacion->id,
                    'producto_id'    => $producto['producto_id'],
                    'cantidad'       => $producto['cantidad'],
                    'precio_unitario'=> $producto['precio_unitario'],
                    'total'          => $producto['total'],
                ]);
            }
        });

        return redirect()->route('cotizaciones.index')->with('success', 'Cotización creada correctamente.');
    } */

    public function store(Request $request){

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos'  => 'required|array',
            'productos.*' => 'required|integer|exists:productos,id',
            'cantidades' => 'required|array',
            'precios'    => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            $productos  = $request->productos;   // array de IDs
            $cantidades = $request->cantidades;  // array de cantidades
            $precios    = $request->precios;     // array de precios unitarios

            $subtotal = 0;
            $detalles = [];

            foreach ($productos as $i => $productoId) {
                $cantidad = (int)($cantidades[$i] ?? 1);
                $precio   = (float)($precios[$i] ?? 0);
                $lineaTotal = $cantidad * $precio;

                $subtotal += $lineaTotal;

                $detalles[] = [
                    'producto_id'     => $productoId,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precio,
                    'total'           => $lineaTotal, // tu campo real en cotizacion_detalles
                ];
            }

            // Ajusta según tu lógica fiscal
            $impuestos = $subtotal * 0.16;
            $total     = $subtotal + $impuestos;

            // Crear la cotización
            $cotizacion = Cotizacion::create([

                'cliente_id' => $request->cliente_id,
                'user_id'    => auth()->id(),
                'fecha'      => now(),
                'subtotal'   => $subtotal,
                'impuestos'  => $impuestos,
                'total'      => $total,
                'estado'     => 'pendiente',
            ]);

            // Crear detalles
            foreach ($detalles as $detalle) {
                $cotizacion->detalles()->create($detalle);
            }
        });

        return redirect()->route('cotizaciones.index')->with('success', 'Cotización creada correctamente.');
    }


    /**
     * Ver detalle de una cotización
     */
    public function show($id){
        $cotizacion = Cotizacion::with(['cliente', 'detalles.producto'])->findOrFail($id);

        // Traer la empresa (siempre 1, o la que necesite)
        $empresa = Empresa::first();
        return view('modulos.cotizaciones.show', compact('cotizacion','empresa'));
    }

    /**
     * Formulario de edición de cotización
     */
    public function edit($id){
        $cotizacion = Cotizacion::with('detalles.producto')->findOrFail($id);
        $clientes = Cliente::all();
        $productos = Producto::all();

        return view('modulos.cotizaciones.edit', compact('cotizacion', 'clientes', 'productos'));
    }

    /**
     * Actualizar cotización
    */
    public function update(Request $request, $id){

        $cotizacion = Cotizacion::findOrFail($id);

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos'  => 'required|array',
            'productos.*' => 'required|integer|exists:productos,id',
            'cantidades' => 'required|array',
            'precios'    => 'required|array',
        ]);

        DB::transaction(function () use ($request, $cotizacion) {
            $productos  = $request->productos;   // array de IDs
            $cantidades = $request->cantidades;  // array de cantidades
            $precios    = $request->precios;     // array de precios unitarios

            $subtotal = 0;
            $detalles = [];

            foreach ($productos as $i => $productoId) {
                $cantidad = (int)($cantidades[$i] ?? 1);
                $precio   = (float)($precios[$i] ?? 0);
                $lineaTotal = $cantidad * $precio;

                $subtotal += $lineaTotal;

                $detalles[] = [
                    'cotizacion_id'   => $cotizacion->id,
                    'producto_id'     => $productoId,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precio,
                    'total'           => $lineaTotal, //aquí usas tu campo real
                ];
            }

            //Ajusta el cálculo de impuestos a tu lógica real (ejemplo IVA 16%)
            $impuestos = $subtotal * 0.16;
            $total     = $subtotal + $impuestos;

            // Actualizar la cotización
            $cotizacion->update([
                'cliente_id' => $request->cliente_id,
                'subtotal'   => $subtotal,
                'impuestos'  => $impuestos,
                'total'      => $total,
            ]);

            // limpiar detalles previos
            $cotizacion->detalles()->delete();

            // insertar nuevos
            foreach ($detalles as $detalle) {
                CotizacionDetalle::create($detalle);
            }
        });

        return redirect()->route('cotizaciones.index')->with('success', 'Cotización actualizada.');
    }


    /**
     * Convertir cotización en venta
    */
    public function convertirEnVenta($id){
        $cotizacion = Cotizacion::with('detalles')->findOrFail($id);

        if ($cotizacion->estado !== 'pendiente') {
            return back()->with('error', 'La cotización ya fue procesada.');
        }elseif($cotizacion->estado === 'convertida'){
            return back()->with('error', 'Esta cotización ya fue convertida en venta.');
        }


        // buscar caja abierta
        $caja = Caja::getCajaActivaByUser(auth()->id());
        if (!$caja) {
            DB::rollback();
            return back()->with('error', 'Debes tener una caja abierta para convertir cotización en venta.');
        }

        DB::transaction(function () use ($cotizacion, $caja) {

            // Generar folio consecutivo - Obtener o crear el folio actual con bloqueo
            $folio = \App\Models\Folio::lockForUpdate()->firstOrCreate(
                ['serie' => '001'],
                ['ultimo_numero' => 0]
            );

            // Incrementar y guardar en la bd
            $folio->ultimo_numero += 1;
            $folio->save();


            // Aquí creas la venta y descuentas inventario
            $venta = Venta::create([

                'user_id'    => auth()->id(),
                'cliente_id' => $cotizacion->cliente_id,
                'empresa_id' => 1,
                'caja_id'    => $caja->id,
                'total_venta'      => $cotizacion->total,
                'folio'        => $folio->serie . '-' . str_pad($folio->ultimo_numero, 6, '0', STR_PAD_LEFT), //Usar el folio generado

            ]);

            foreach ($cotizacion->detalles as $detalle) {

                DetalleVenta::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $detalle->producto_id,
                    'cantidad'        => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'sub_total'       => $detalle->total, //$detalle->total viene de la tabla cotizaciónes
                ]);

                // descontar inventario Actualizar stock
                $detalle->producto->decrement('cantidad', $detalle->cantidad);
            }

            // actualizar cotización
            $cotizacion->update(['estado' => 'convertida']);

            // actualizar caja
            $caja->increment('total_ventas', $venta->total_venta); //$venta->total_venta viene de la tabla ventas

        });

        return redirect()->route('detalleventas.index')->with('success', 'Cotización convertida en venta correctamente.');
    }

    /**
     * Cancelar cotización
     */
    public function destroy($id){
        $cotizacion = Cotizacion::findOrFail($id);

        if($cotizacion->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden cancelar cotizaciones pendientes.');
        }

        $cotizacion->update(['estado' => 'cancelada']);

        return redirect()->route('cotizaciones.index')->with('success', 'Cotización cancelada.');
    }


    public function descargarPdf($id){
        $cotizacion = Cotizacion::with(['cliente', 'detalles.producto'])->findOrFail($id);
        $empresa = Empresa::first(); // o datos fijos

        $pdf = Pdf::loadView('modulos.cotizaciones.pdf', compact('cotizacion', 'empresa'))
                ->setPaper('A4', 'portrait');

        return $pdf->stream('cotizacion_'.$cotizacion->id.'.pdf');
    }
}
