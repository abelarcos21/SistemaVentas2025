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

use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Illuminate\Support\Facades\Storage;
use Luecano\NumeroALetras\NumeroALetras;
use Yajra\DataTables\Facades\DataTables;

class DetalleVentasController extends Controller
{
    //index
    public function index(Request $request){

        if ($request->ajax()) {
            $ventas = Venta::with(['user', 'detalles.producto'])
                ->select([
                    'ventas.id',
                    'ventas.folio',
                    'ventas.total_venta',
                    'ventas.estado',
                    'ventas.created_at',
                    'ventas.user_id',
                    'ventas.cliente_id'
                ]);

            return DataTables::of($ventas)
                ->addIndexColumn()
                ->addColumn('usuario', function ($venta) {
                    return $venta->user ? $venta->user->name : 'Sin Usuario';
                })
                ->addColumn('cliente', function ($venta) {
                    if ($venta->cliente) {
                        return trim($venta->cliente->nombre . ' ' . ($venta->cliente->apellido ?? ''));
                    }
                    return 'Sin cliente';
                })
                ->addColumn('total_formateado', function ($venta) {
                    $totalVendido = '$' . number_format($venta->total_venta, 2);
                    return '<span class="text-primary fw-bold">' . $totalVendido . '</span>';
                })
                ->addColumn('folio_formateado', function ($venta) {
                    return '<span class="text-primary fw-bold">' . $venta->folio . '</span>';
                })
                ->addColumn('fecha_formateada', function ($venta) {
                    return $venta->created_at->format('d/m/Y h:i a');
                })
                ->addColumn('estado_badge', function ($venta) {
                    $badgeClass = match($venta->estado) {
                        'completada' => 'bg-success',
                        'cancelada' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($venta->estado) . '</span>';
                })
                ->addColumn('ver_detalle', function ($venta) {
                    return '<a href="' . route('detalleventas.detalle_venta', $venta->id) . '"
                            class="btn btn-info bg-gradient-info btn-sm">
                            <i class="fas fa-eye"></i> Ver
                            </a>';
                })
                ->addColumn('imprimir_ticket', function ($venta) {
                    return '<a target="_blank" href="' . route('detalle.ticket', $venta->id) . '"
                            class="btn btn-success bg-gradient-success btn-sm">
                            <i class="fas fa-print"></i> Ticket
                            </a>';
                })
                ->addColumn('boleta_venta', function ($venta) {
                    return '<a target="_blank" href="' . route('detalle.boleta', $venta->id) . '"
                            class="btn btn-secondary bg-gradient-secondary btn-sm">
                            <i class="fas fa-print"></i> Boleta
                            </a>';
                })
                ->addColumn('acciones', function ($venta) {
                    if ($venta->estado === 'completada') {
                        return '<form action="' . route('detalle.revocar', $venta->id) . '"
                                method="POST" class="formulario-eliminar">
                                ' . csrf_field() . '
                                <button class="btn btn-danger bg-gradient-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i> Cancelar
                                </button>
                                </form>';
                    }
                    return '<span class="text-muted">Sin acciones</span>';
                })
                ->rawColumns(['estado_badge', 'ver_detalle', 'imprimir_ticket', 'boleta_venta', 'total_formateado','folio_formateado','acciones'])
                ->make(true);
        }

        return view('modulos.detalleventas.index');
    }

    // Método para mostrar detalles específicos de una venta(2 OPCION)
    public function detalleVenta($id)
    {
        $venta = Venta::with(['detalles.producto', 'user'])->findOrFail($id);

        return view('modulos.detalleventas.detalle_venta', compact('venta'));
    }

    // Método para DataTable de detalles de venta específica(2 OPCION)
    /* public function detalleVentaData(Request $request, $ventaId)
    {
        if ($request->ajax()) {
            $detalles = DetalleVenta::with('producto')
                ->where('venta_id', $ventaId)
                ->select([
                    'detalle_ventas.id',
                    'detalle_ventas.producto_id',
                    'detalle_ventas.tipo_precio_aplicado',
                    'detalle_ventas.precio_unitario_aplicado',
                    'detalle_ventas.descuento_aplicado',
                    'detalle_ventas.cantidad',
                    'detalle_ventas.sub_total'
                ]);

            return DataTables::of($detalles)
                ->addIndexColumn()
                ->addColumn('producto_nombre', function ($detalle) {
                    return $detalle->producto ? $detalle->producto->nombre : 'Producto no encontrado';
                })
                ->addColumn('precio_formateado', function ($detalle) {
                    return 'MXN $' . number_format($detalle->precio_unitario_aplicado, 2);
                })
                ->addColumn('descuento_formateado', function ($detalle) {
                    return 'MXN $' . number_format($detalle->descuento_aplicado, 2);
                })
                ->addColumn('subtotal_formateado', function ($detalle) {
                    return 'MXN $' . number_format($detalle->sub_total, 2);
                })
                ->addColumn('tipo_precio_badge', function ($detalle) {
                    $badgeClass = match($detalle->tipo_precio_aplicado) {
                        'base' => 'bg-primary',
                        'mayoreo' => 'bg-info',
                        'oferta' => 'bg-warning',
                        default => 'bg-secondary'
                    };
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($detalle->tipo_precio_aplicado) . '</span>';
                })
                ->rawColumns(['tipo_precio_badge'])
                ->make(true);
        }

        return response()->json(['error' => 'Acceso no autorizado'], 403);
    } */

    /**
    * Obtener datos de productos vendidos para DataTable
    */
    public function getProductosVendidos($ventaId)
    {
        $detalles = DB::table('detalle_venta as dv')
            ->join('productos as p', 'dv.producto_id', '=', 'p.id')
            ->leftJoin('categorias as c', 'p.categoria_id', '=', 'c.id')
            ->leftJoin('marcas as m', 'p.marca_id', '=', 'm.id')
            ->leftJoin('imagens as img', 'p.id', '=', 'img.producto_id')
            ->select([
                'dv.id',
                'p.nombre as producto_nombre',
                'dv.tipo_precio_aplicado',
                'c.nombre as categoria_nombre',
                'm.nombre as marca_nombre',
                'dv.cantidad',
                'dv.precio_unitario_aplicado',
                'dv.descuento_aplicado',
                'dv.sub_total',
                'img.ruta as imagen_ruta'
            ])
            ->where('dv.venta_id', $ventaId);

        return DataTables::of($detalles)
            ->addColumn('imagen', function ($detalle) {
                if ($detalle->imagen_ruta) {
                    return '<img src="' . asset('storage/' . $detalle->imagen_ruta) . '"
                            alt="' . $detalle->producto_nombre . '"
                            width="50" height="50" class="rounded">';
                }
                return '<span class="text-muted">Sin imagen</span>';
            })
            ->addColumn('categoria', function ($detalle) {
                return '<small class="text-muted">
                            <i class="fas fa-tag mr-1"></i>
                            Categoría: <span class="badge badge-secondary" style="font-size: 12px;">' .
                            ($detalle->categoria_nombre ?? 'Sin categoría') . '</span>
                        </small>';
            })
            ->addColumn('marca', function ($detalle) {
                return '<small class="text-muted">
                            <i class="fas fa-trademark mr-1"></i>
                            Marca: <span class="badge badge-secondary" style="font-size: 12px;">' .
                            ($detalle->marca_nombre ?? 'Sin marca') . '</span>
                        </small>';
            })
            ->addColumn('cantidad_badge', function ($detalle) {
                return '<span class="badge badge-primary badge-pill px-3 py-2" style="font-size: 13px;">' .
                    $detalle->cantidad . '</span>';
            })
            ->addColumn('precio_formateado', function ($detalle) {
                return '<span class="text-success">$' . number_format($detalle->precio_unitario_aplicado, 2) . '</span>';
            })
            ->addColumn('descuento_formateado', function ($detalle) {
                return '<span class="text-warning">$' . number_format($detalle->descuento_aplicado, 2) . '</span>';
            })
            ->addColumn('subtotal_formateado', function ($detalle) {
                return '<span class="text-primary">$' . number_format($detalle->sub_total, 2) . '</span>';
            })
            ->rawColumns(['imagen', 'categoria', 'marca', 'cantidad_badge', 'precio_formateado', 'descuento_formateado', 'subtotal_formateado'])
            ->make(true);
    }

    //METODO PARA EL DETALLE DE UNA VENTA
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
            'producto.marca',       // 👈 Relación con la marca
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
        /*  $qr = base64_encode(QrCode::format('png')->size(100)->generate('http://sistemaventas2025.test:8080')); */
        // Intentar generar el QR (compatibilidad local + AlwaysData)
        try {
            // En local: usa el método normal de Simple QrCode
            $qr = base64_encode(
                QrCode::format('png')
                    ->size(100)
                    ->generate('http://sistemaventas2025.test:8080/ticket/'.$venta->id)
            );
        } catch (\Exception $e) {
            // Generar QR como SVG (compatible con AlwaysData)
            $renderer = new ImageRenderer(
                new RendererStyle(100),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);

            $qr = base64_encode(
                $writer->writeString('https://clickventa.alwaysdata.net/ticket/'.$venta->id)
            );
        }

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
        /* $qr = base64_encode(QrCode::format('png')->size(150)->generate($qrContenido)); */
        // Intentar generar el QR (compatibilidad local + AlwaysData)
        try {
            // En local: usa el método normal de Simple QrCode
            $qr = base64_encode(
                QrCode::format('png')
                    ->size(150)
                    ->generate('http://sistemaventas2025.test/boleta/'.$venta->id)
            );
        } catch (\Exception $e) {
            // Generar QR como SVG (compatible con AlwaysData)
            $renderer = new ImageRenderer(
                new RendererStyle(150),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);

            $qr = base64_encode($writer->writeString($qrContenido));
        }

        // Generar PDF
        $pdf = Pdf::loadView('modulos.detalleventas.boleta', compact(
            'nota', 'detalles', 'venta', 'qr', 'logoBase64',
        ))->setPaper('A4', 'portrait');

        // Opcional: borrar después si no se necesita guardar
        // Storage::disk('public')->delete($filename);

        return $pdf->stream('boleta_mexico.pdf');
    }

}
