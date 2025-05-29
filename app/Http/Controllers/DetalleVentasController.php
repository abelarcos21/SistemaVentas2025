<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use App\Models\DetalleVenta;
use App\Models\Producto;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

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

        /* Anteriormente, este método eliminaba físicamente la venta y sus detalles de la base de datos,
        lo cual no es ideal para un sistema de historial,
        ya que no podrías rastrear ventas canceladas o eliminadas más adelante. */

       /*  Recomendación: Usar lógica de "cancelación" o "eliminación" con estado
        En lugar de borrar registros, lo ideal es actualizar el campo estado de la venta para
        reflejar que ha sido cancelada o eliminada, y devolver el stock. */
        DB::beginTransaction();
        try {

            $venta = Venta::findOrFail($id);

            // Verificar si ya está cancelada o eliminada
            if(in_array($venta->estado, ['cancelada','eliminada'])){
                return to_route('detalleventas.index')->with('error','La venta ya ha sido cancelada o eliminada.');

            }

            /* $detalles = DetalleVenta::select(
                'producto_id', 'cantidad'
            )
            ->where('venta_id', $id)
            ->get(); */

            $detalles = DetalleVenta::Where('venta_id',$venta->id)->get();

            //devolver stock
            foreach($detalles as $detalle) {
                Producto::where('id', $detalle->producto_id)
                ->increment('cantidad', $detalle->cantidad);
            }

            /* //eliminar productos vendidos y la venta
            DetalleVenta::where('venta_id', $id)->delete();
            Venta::where('id', $id)->delete(); */

            //cambiar estado de la venta(puedes usar cancelada o eliminada)
            $venta->estado = 'cancelada'; // o 'eliminada' si quieres un tipo más fuerte
            $venta->save();

            DB::commit();
            return to_route('detalleventas.index')->with('success', '¡Venta cancelada exitosamente!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return to_route('detalleventas.index')->with('error', '¡No se pudo cancelar la venta! ' . $th->getMessage());
        }
    }

    public function generarTicket($id){

        $venta = Venta::select(
            'ventas.*',
            'users.name as nombre_usuario',
            'clientes.nombre as nombre_cliente'
        )
        ->join('users', 'ventas.user_id', '=', 'users.id')//agregar el nombre del usuario quien hiso la venta
        ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')//agregar el nombre del cliente
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

    public function generarBoleta(){

        $cliente = [
            'nombre' => 'Carlos Martínez',
            'documento' => 'MARC850101HDFLRS08', // puede ser RFC o CURP
            'direccion' => 'Calle Reforma 45, Guadalajara, Jal.',
            'telefono' => '3312345678',
        ];

        $items = [
            ['nombre' => 'Laptop HP', 'cantidad' => 1, 'precio' => 12500.00],
            ['nombre' => 'Mouse inalámbrico', 'cantidad' => 2, 'precio' => 350.00],
            ['nombre' => 'Monitor Samsung 27"', 'cantidad' => 1, 'precio' => 4500.00],
        ];

        $total = collect($items)->sum(fn($item) => $item['cantidad'] * $item['precio']);
        $nota = 'Gracias por su compra. No se aceptan devoluciones pasadas 24h.';
        $folio = '001-000369'; // puedes hacerlo dinámico si tienes una tabla ventas

        // Contenido para el QR
        $qrContenido = "BOLETA DE VENTA\n";
        $qrContenido .= "Cliente: {$cliente['nombre']}\n";
        $qrContenido .= "RFC/CURP: {$cliente['documento']}\n";
        $qrContenido .= "Total: $" . number_format($total, 2) . "\n";
        $qrContenido .= "Folio: {$folio}\n";
        $qrContenido .= "Validación: Comercializadora México";


        // Generar QR y guardar en disco
        $qrImage = QrCode::format('png')->size(150)->generate($qrContenido);
        $filename = 'qr_temp_' . uniqid() . '.png';
        Storage::disk('public')->put($filename, $qrImage);
        $qrPath = public_path('storage/' . $filename);

        // Generar PDF
        $pdf = Pdf::loadView('modulos.detalleventas.boleta', compact(
            'cliente', 'items', 'total', 'nota', 'qrPath', 'folio'
        ))->setPaper('A4', 'portrait');

        // Opcional: borrar después si no se necesita guardar
        // Storage::disk('public')->delete($filename);

        return $pdf->stream('boleta_mexico.pdf');
    }

}
