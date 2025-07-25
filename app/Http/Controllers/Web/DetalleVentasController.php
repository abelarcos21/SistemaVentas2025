<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Empresa;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Luecano\NumeroALetras\NumeroALetras;

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

       /*  $detalles = DetalleVenta::select(
            'detalle_venta.*',
            'productos.nombre as nombre_producto',
        )
        ->join('productos', 'detalle_venta.producto_id', '=', 'productos.id')
        ->where('venta_id', $id)
        ->get(); */

        /* $detalles = DetalleVenta::with('producto.imagen') // 👈 Trae producto e imagen en una sola consulta y acceder alos campos de tabla producto
        ->where('venta_id', $id)
        ->get(); */

        $detalles = DetalleVenta::with([
            'producto.imagen',     // 👈 Trae producto e imagen en una sola consulta y acceder alos campos de tabla producto
            'producto.categoria',  // 👈 Relación con la categoría
            'producto.marca'       // 👈 Relación con la marca
        ])
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
            'clientes.nombre as nombre_cliente',
            'clientes.apellido as apellido_cliente',

            //agregar datos de la empresa negocio
            'empresas.razon_social as razon_social_empresa',
            'empresas.rfc as rfc_empresa',
            'empresas.direccion as direccion_empresa',
            'empresas.codigo_postal as codigo_postal_empresa',
            'empresas.telefono as telefono_empresa',
            'empresas.correo as correo_empresa',
            'empresas.imagen as imagen_empresa'
        )
        ->join('users', 'ventas.user_id', '=', 'users.id')//agregar el nombre del usuario quien hiso la venta
        ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')//agregar el nombre del cliente quien hizo la compra
        ->join('empresas', 'ventas.empresa_id', '=', 'empresas.id') // agregar los datos de la empresa negocio
        ->where('ventas.id', $id)
        ->firstOrFail();

        // Convertir imagen a base64 para DomPDF
        $logoBase64 = null;
        if ($venta->imagen_empresa) {
            $imagePath = storage_path('app/public/' . $venta->imagen_empresa);
            if (file_exists($imagePath)) {
                $imageData = file_get_contents($imagePath);
                $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
                $logoBase64 = 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
            }
        }

        $detalles = DetalleVenta::select(
            'detalle_venta.*',
            'productos.nombre as nombre_producto'
        )
        ->join('productos', 'detalle_venta.producto_id', '=', 'productos.id')
        ->where('venta_id', $id)
        ->get();

        //FORMATEO EN LETRAS LA CANTIDAD TOTAL VENTA
        $formatter = new NumeroALetras();
        $monto = number_format($venta->total_venta, 2, '.', ''); // Asegura 2 decimales
        $partes = explode('.', $monto);

        $parteEntera = (int) $partes[0];
        $centavos = isset($partes[1]) ? str_pad($partes[1], 2, '0', STR_PAD_RIGHT) : '00';

        $letra = $formatter->toWords($parteEntera);
        $totalLetras = ucfirst($letra) . " PESOS {$centavos}/100 M.N.";
        //////////////////////////////
        $pagos = $venta->pagos;  //METODOS DE PAGO EFECTIVO/TARJETA/TRANSFERENCIA
        $efectivoTotal = $pagos->sum('monto'); //MONTO QUE PAGO TOTAL
        $cambio = $efectivoTotal - $venta->total_venta; //SU CAMBIO

        $totalArticulos = $detalles->sum('cantidad');//CANTIDAD DE ARTICULOS VENDIDOS

        // Generar QR
        $qr = base64_encode(QrCode::format('png')->size(100)->generate('http://sistemaventas2025.test:8080'));

        // Generar PDF con tamaño personalizado tipo ticket (80mm x altura ajustable)
        $pdf = Pdf::loadView('modulos.detalleventas.ticket', compact('venta', 'detalles', 'qr', 'totalLetras','efectivoTotal','cambio', 'pagos','totalArticulos', 'logoBase64'))
                ->setPaper([0, 0, 300, 900], 'portrait'); // 80mm de ancho

        return $pdf->stream("ticket_compra_{$venta->id}.pdf");
    }

    public function generarBoleta($id){

        $venta = Venta::select(
            'ventas.*',
            'users.name as nombre_usuario',
            'clientes.nombre as nombre_cliente',//nombre del cliente
            'clientes.apellido as apellido_cliente',//apellido del cliente
            'clientes.correo as correo_cliente',//correo del cliente
            'clientes.rfc as rfc_cliente', // puede ser RFC o CURP
            'clientes.telefono as telefono_cliente',
            //agregar datos de la empresa negocio
            'empresas.razon_social as razon_social_empresa',
            'empresas.rfc as rfc_empresa',
            'empresas.direccion as direccion_empresa',
            'empresas.telefono as telefono_empresa',
            'empresas.correo as correo_empresa',
            'empresas.imagen as imagen_empresa'
        )
        ->join('users', 'ventas.user_id', '=', 'users.id')//agregar el nombre del usuario quien hiso la venta
        ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')//agregar el nombre del cliente quien hiso la compra
        ->join('empresas', 'ventas.empresa_id', '=', 'empresas.id') // agregar los datos de la empresa negocio
        ->where('ventas.id', $id)
        ->firstOrFail();

        // Convertir imagen a base64 para DomPDF
        $logoBase64 = null;
        if ($venta->imagen_empresa) {
            $imagePath = storage_path('app/public/' . $venta->imagen_empresa);
            if (file_exists($imagePath)) {
                $imageData = file_get_contents($imagePath);
                $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
                $logoBase64 = 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
            }
        }

        $detalles = DetalleVenta::select( //detalles de la venta
            'detalle_venta.*',
            'productos.nombre as nombre_producto'
        )
        ->join('productos', 'detalle_venta.producto_id', '=', 'productos.id')
        ->where('venta_id', $id)
        ->get();

        $nota = 'Gracias por su compra. No se aceptan devoluciones pasadas 24h.';

        // Contenido para el QR con datos del cliente y datos de empresa
        $qrContenido = "BOLETA DE VENTA\n";
        $qrContenido .= "Empresa: {$venta->razon_social_empresa}\n";
        $qrContenido .= "RFC Empresa: {$venta->rfc_empresa}\n";
        $qrContenido .= "Cliente: {$venta->nombre_cliente}\n";
        $qrContenido .= "RFC/CURP: {$venta->rfc_cliente}\n";
        $qrContenido .= "Total: $" . number_format($venta->total_venta, 2) . "\n";
        $qrContenido .= "Folio: {$venta->folio}\n";
        $qrContenido .= "Validación: {$venta->razon_social_empresa}";

        // Generar QR
        $qr = base64_encode(QrCode::format('png')->size(150)->generate($qrContenido));

        // Generar PDF
        $pdf = Pdf::loadView('modulos.detalleventas.boleta', compact(
            'nota', 'detalles', 'venta', 'qr', 'logoBase64',
        ))->setPaper('A4', 'portrait');

        // Opcional: borrar después si no se necesita guardar
        // Storage::disk('public')->delete($filename);

        return $pdf->stream('boleta_mexico.pdf');
    }

}
