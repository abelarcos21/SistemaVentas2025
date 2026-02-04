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
use App\Models\Pago;
use App\Models\Empresa;
use App\Models\FolioCotizacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode as EndroidQrCode;


class CotizacionController extends Controller
{

    /**
     * Mostrar listado de cotizaciones
     */
    public function index(Request $request){

        if ($request->ajax()) {

            $cotizaciones = Cotizacion::with(['cliente', 'venta', 'usuario'])->select('cotizaciones.*');

            return DataTables::eloquent($cotizaciones)
                ->addIndexColumn()
                ->addColumn('folio', function ($cotizacion) {
                    return '<span class="badge bg-primary">' . ($cotizacion->folio ?? 'N/A') . '</span>';
                })
                ->addColumn('cliente', function ($cotizacion) {
                    return $cotizacion->cliente
                        ? $cotizacion->cliente->nombre . ' ' . $cotizacion->cliente->apellido
                        : 'PÚBLICO GENERAL';
                })
                ->addColumn('fecha', function ($cotizacion) {
                    return $cotizacion->fecha
                        ? \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y h:i a')
                        : 'N/A';
                })
                ->addColumn('total', function ($cotizacion) {
                    return '$' . number_format($cotizacion->total, 2);
                })
                ->addColumn('vigencia', function ($cotizacion) {
                    if ($cotizacion->estado !== 'pendiente') {
                        return '<span class="badge bg-secondary">N/A</span>';
                    }

                    // Calcular días restantes
                    $fechaVencimiento = \Carbon\Carbon::parse($cotizacion->fecha)
                        ->addDays($cotizacion->vigencia_dias);
                    $diasRestantes = now()->diffInDays($fechaVencimiento, false);

                    if ($diasRestantes < 0) {
                        return '<span class="badge bg-danger">
                                    <i class="fas fa-exclamation-circle"></i> Vencida
                                </span>';
                    } elseif ($diasRestantes <= 7) {
                        return '<span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock"></i> ' . abs($diasRestantes) . ' días
                                </span>';
                    } else {
                        return '<span class="badge bg-success">
                                    <i class="fas fa-check"></i> ' . $diasRestantes . ' días
                                </span>';
                    }
                })
                ->addColumn('estado', function ($cotizacion) {
                    $badgeClass = match($cotizacion->estado) {
                        'pendiente' => 'badge-warning',
                        'convertida' => 'badge-success',
                        'cancelada' => 'badge-danger',
                        default => 'badge-secondary'
                    };

                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($cotizacion->estado) . '</span>';
                })
                ->addColumn('venta', function ($cotizacion) {
                    if ($cotizacion->estado === 'cancelada') {
                        return '<span class="badge bg-danger">
                                    <i class="fas fa-ban"></i> Cotización Cancelada
                                </span>';
                    }

                    if ($cotizacion->venta) {
                        return '<a href="' . route('detalleventas.detalle_venta', $cotizacion->venta->id) . '"
                                class="btn btn-sm bg-gradient-success">
                                    <i class="fas fa-receipt"></i> Venta Nro.  ' . $cotizacion->venta->folio . '
                                </a>';
                    }

                    if ($cotizacion->estado === 'convertida' && !$cotizacion->venta) {
                        return '<span class="badge bg-warning text-dark">
                                    <i class="fas fa-exclamation-triangle"></i> Venta no encontrada
                                </span>';
                    }

                    return '<span class="badge bg-secondary">
                                <i class="fas fa-clock"></i> Pendiente
                            </span>';
                })
                ->addColumn('acciones', function ($cotizacion) {
                    $acciones = '
                        <div class="d-flex justify-content-center gap-1" style="gap: 0.25rem;">
                            <a href="' . route('cotizaciones.show', $cotizacion) . '" class="btn bg-gradient-info btn-sm" title="Ver Detalle">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a target="_blank" href="' . route('cotizaciones.pdf', $cotizacion->id) . '" class="btn bg-gradient-secondary btn-sm" title="Ver PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                    ';

                    if ($cotizacion->estado === 'pendiente') {
                        $acciones .= '
                            <a href="' . route('cotizaciones.edit', $cotizacion) . '" class="btn bg-gradient-primary btn-sm" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="' . route('cotizaciones.convertir', $cotizacion->id) . '"
                            class="btn bg-gradient-success btn-sm"
                            title="Convertir a Venta">
                                <i class="fas fa-cash-register"></i>
                            </a>
                            <button type="button" class="btn bg-gradient-danger btn-sm btn-cancelar"
                                data-id="' . $cotizacion->id . '"
                                data-url="' . route('cotizaciones.destroy', $cotizacion) . '"
                                title="Cancelar Cotizacion">
                                <i class="fas fa-trash"></i>
                            </button>';
                    }

                    if ($cotizacion->estado === 'convertida') {
                        $acciones .= '
                            <span class="text-muted">Convertida</span>';

                    }

                    if ($cotizacion->estado === 'cancelada') {
                        $acciones .= '
                            <span class="text-muted">Cancelada</span>';
                    }

                    $acciones .= '</div>';
                    return $acciones;
                })
                ->filter(function ($query) use ($request) {
                    if ($request->filled('cliente_id') && $request->cliente_id != '') {
                        $query->where('cliente_id', $request->cliente_id);
                    }

                    if ($request->filled('estado') && $request->estado != '') {
                        $query->where('estado', $request->estado);
                    }

                    if ($request->filled('fecha_desde') && $request->fecha_desde != '') {
                        $query->whereDate('fecha', '>=', $request->fecha_desde);
                    }

                    if ($request->filled('fecha_hasta') && $request->fecha_hasta != '') {
                        $query->whereDate('fecha', '<=', $request->fecha_hasta);
                    }

                    // Filtro de vigencia
                    if ($request->filled('vigentes') && $request->vigentes == '1') {
                        $query->where('estado', 'pendiente')
                            ->whereRaw('DATE_ADD(fecha, INTERVAL vigencia_dias DAY) >= CURDATE()');
                    }

                    if ($request->filled('vencidas') && $request->vencidas == '1') {
                        $query->where('estado', 'pendiente')
                            ->whereRaw('DATE_ADD(fecha, INTERVAL vigencia_dias DAY) < CURDATE()');
                    }
                })
                ->rawColumns(['folio', 'vigencia', 'estado', 'venta', 'acciones'])
                ->make(true);
        }

        $clientes = Cliente::orderBy('nombre')->get();

        return view('modulos.cotizaciones.index', compact('clientes'));
    }

    /**
     * Formulario de creación de cotización
    */
    public function create(){
        $clientes = Cliente::all();
        $productos = Producto::where('cantidad', '>', 0)->get();
        return view('modulos.cotizaciones.create', compact('clientes', 'productos'));
    }

    // Mostrar formulario de conversión a venta (aquí se pueden modificar productos)
    public function mostrarFormularioConversion($id){
        $cotizacion = Cotizacion::with('detalles.producto')->findOrFail($id);

        if ($cotizacion->estado !== 'pendiente') {
            return back()->with('error', 'Esta cotización ya fue procesada.');
        }

        // Verificar vigencia
        if (!$cotizacion->estaVigente()) {
            return back()->with('warning', 'Esta cotización está vencida. Puedes procesarla pero considera renovar los precios.');
        }

        // Verificar caja abierta
        $caja = Caja::getCajaActivaByUser(auth()->id());
        if (!$caja) {
            return back()->with('error', 'Debes tener una caja abierta para convertir cotización en venta.');
        }

        $productos = Producto::where('cantidad', '>', 0)->get();

        return view('modulos.cotizaciones.convertir', compact('cotizacion', 'productos', 'caja'));
    }

    // convertir cotizacion en venta Procesar la conversión con los productos modificados
    public function convertirEnVenta(Request $request, $id){
        $cotizacion = Cotizacion::with('detalles')->findOrFail($id);

        if ($cotizacion->estado !== 'pendiente') {
            return back()->with('error', 'La cotización ya fue procesada.');
        }

        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario_aplicado' => 'required|numeric|min:0.01',
        ]);

        // Buscar caja abierta
        $caja = Caja::getCajaActivaByUser(auth()->id());
        if (!$caja) {
            return back()->with('error', 'Debes tener una caja abierta.');
        }

        DB::beginTransaction();
        try {
            // Generar folio de venta  consecutivo
            $folio = \App\Models\Folio::lockForUpdate()->firstOrCreate(
                ['serie' => '001'],
                ['ultimo_numero' => 0]
            );
            $folio->ultimo_numero += 1;
            $folio->save();

            // Calcular total de los productos enviados
            $totalVenta = 0;
            foreach ($request->productos as $prod) {
                $totalVenta += $prod['cantidad'] * $prod['precio_unitario_aplicado'];
            }

            // Crear la venta con los productos modificados
            $venta = Venta::create([
                'user_id'      => auth()->id(),
                'cliente_id'   => $cotizacion->cliente_id,
                'empresa_id'   => 1,
                'caja_id'      => $caja->id,
                'total_venta'  => $totalVenta,
                'folio'        => $folio->serie . '-' . str_pad($folio->ultimo_numero, 6, '0', STR_PAD_LEFT),
                'cotizacion_id' => $cotizacion->id,
            ]);

            // Crear detalles de venta y descontar inventario
            foreach ($request->productos as $prod) {
                $producto = Producto::findOrFail($prod['producto_id']);

                // Validar stock
                if ($producto->cantidad < $prod['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre}. Disponible: {$producto->cantidad}");
                }

                // Recuperar datos enviados desde el formulario
                $cantidad = (int) ($prod['cantidad'] ?? 1);
                $precioAplicado = (float) ($prod['precio_unitario_aplicado'] ?? 0);

                //Inicializar la variable en cada vuelta por defecto
                $tipoPrecio = 'base';

                //al convertir a venta, las condiciones (fecha, cantidad) pueden haber cambiado.
                // Es mejor recalcular qué precio se está aplicando realmente.

                // --- LÓGICA DE DETECCIÓN AUTOMÁTICA ---
                // Comparamos el precio que llega del JS contra los precios reales de la BD

                //Verificar si coincide con precio OFERTA
                $coincideOferta = false;
                if ($producto->en_oferta && $producto->fecha_fin_oferta >= now()) {
                    if (abs($precioAplicado - $producto->precio_oferta) < 0.01) {
                        $coincideOferta = true;
                    }
                }

                // Verificar si coincide con precio MAYOREO
                $coincideMayoreo = false;
                if ($producto->precio_mayoreo > 0 && $cantidad >= $producto->cantidad_minima_mayoreo) {
                    if (abs($precioAplicado - $producto->precio_mayoreo) < 0.01) {
                        $coincideMayoreo = true;
                    }
                }

                //Asignación Final (Jerarquía)
                if ($coincideMayoreo) {
                    // Si coincide con mayoreo, TIENE PRIORIDAD (o la lógica que prefieras)
                    $tipoPrecio = 'mayoreo';
                } elseif ($coincideOferta) {
                    $tipoPrecio = 'oferta';
                } elseif (abs($precioAplicado - $producto->precio_venta) < 0.01) {
                    $tipoPrecio = 'base';
                } else {
                    $tipoPrecio = 'personalizado'; // El precio no coincide con ninguno (editado manual)
                }

                /* \Log::info('Detección de precio:', [
                    'producto' => $producto->nombre,
                    'precio_aplicado' => $precioAplicado,
                    'cantidad' => $cantidad,
                    'en_oferta' => $producto->en_oferta,
                    'precio_oferta' => $producto->precio_oferta,
                    'precio_mayoreo' => $producto->precio_mayoreo,
                    'cantidad_minima_mayoreo' => $producto->cantidad_minima_mayoreo,
                    'precio_venta' => $producto->precio_venta,
                    'tipo_detectado' => $tipoPrecio
                ]); */

                // Calcular subtotal
                $subtotal = $cantidad * $precioAplicado;

                // Crear detalle de venta
                DetalleVenta::create([
                    'venta_id'                 => $venta->id,
                    'producto_id'              => $prod['producto_id'],
                    'cantidad'                 => $cantidad,
                    'precio_unitario_aplicado' => $precioAplicado,
                    'sub_total'                => $subtotal,
                    'tipo_precio_aplicado'     => $tipoPrecio,
                ]);

                // Descontar inventario
                $producto->decrement('cantidad', $cantidad);
            }

            // Registrar pagos
            $this->registrarPagos($request, $venta);


            // Actualizar cotización
            $cotizacion->update(['estado' => 'convertida']);

            // Actualizar caja
            $caja->increment('total_ventas', $venta->total_venta);

            DB::commit();

            return redirect()
                ->route('cotizaciones.index')
                ->with('success', 'Venta realizada correctamente. Folio: ' . $venta->folio);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error convirtiendo cotización en venta', [
                'cotizacion_id' => $id,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            return back()->with('error', 'Error al procesar venta: ' . $e->getMessage());
        }
    }

    /**
     * Guardar cotización en BD
    */
    public function store(Request $request){

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos'  => 'required|array',
            'productos.*' => 'required|integer|exists:productos,id',
            'cantidades' => 'required|array',
            'precios'    => 'required|array',
            'tipos_precio' => 'required|array',
            'nota' => 'nullable|string|max:500',
            'vigencia_dias' => 'nullable|integer|min:1|max:365',
        ]);

        DB::transaction(function () use ($request) {
            // Generar folio único
            $folio = FolioCotizacion::generarSiguiente();

            $productos  = $request->productos;   // array de IDs
            $cantidades = $request->cantidades;  // array de cantidades
            $precios    = $request->precios;     // array de precios unitarios
            $tiposPrecios = $request->tipos_precio; //tipo de precio

            $subtotal = 0;
            $detalles = [];

            foreach ($productos as $i => $productoId) {
                $cantidad = (int)($cantidades[$i] ?? 1);
                $precio   = (float)($precios[$i] ?? 0);
                $tipoPrecio = $tiposPrecios[$i] ?? 'base';
                $lineaTotal = $cantidad * $precio;

                $subtotal += $lineaTotal;

                $detalles[] = [
                    'producto_id'     => $productoId,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precio,
                    'tipo_precio'     => $tipoPrecio,
                    'total'           => $lineaTotal, // tu campo real en cotizacion_detalles
                ];
            }

            // Ajusta según tu lógica fiscal
            /*  $impuestos = $subtotal * 0.16; */
            /* $total     = $subtotal + $impuestos; */

            $total = $subtotal;

            // Crear la cotización
            $cotizacion = Cotizacion::create([
                'folio'      => $folio,
                'cliente_id' => $request->cliente_id,
                'user_id'    => auth()->id(),
                'fecha'      => now(),
                'subtotal'   => $subtotal,
                /* 'impuestos'  => $impuestos, */
                'total'      => $total,
                'estado'     => 'pendiente',
                'nota'          => $request->nota,
                'vigencia_dias' => $request->vigencia_dias ?? 30,
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
        $cotizacion = Cotizacion::with(['cliente', 'detalles.producto', 'usuario', 'venta'])->findOrFail($id);

        // Traer la empresa (siempre 1, o la que necesite)
        $empresa = Empresa::first();
        return view('modulos.cotizaciones.show', compact('cotizacion','empresa'));
    }

    /**
     * Formulario de edición de cotización
     */
    public function edit($id){
        $cotizacion = Cotizacion::with('detalles.producto')->findOrFail($id);

        if ($cotizacion->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden editar cotizaciones pendientes.');
        }

        $clientes = Cliente::all();
        $productos = Producto::all();

        return view('modulos.cotizaciones.edit', compact('cotizacion', 'clientes', 'productos'));
    }

    /**
     * Actualizar cotización
    */
    public function update(Request $request, $id){

        $cotizacion = Cotizacion::findOrFail($id);

        if ($cotizacion->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden editar cotizaciones pendientes.');
        }

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos'  => 'required|array',
            'productos.*' => 'required|integer|exists:productos,id',
            'cantidades' => 'required|array',
            'precios'    => 'required|array',
            'tipos_precio' => 'required|array',
            'nota' => 'nullable|string|max:500',
            'vigencia_dias' => 'nullable|integer|min:1|max:365',
        ]);

        DB::transaction(function () use ($request, $cotizacion) {
            $productos  = $request->productos;   // array de IDs
            $cantidades = $request->cantidades;  // array de cantidades
            $precios    = $request->precios;     // array de precios unitarios
            $tiposPrecios = $request->tipos_precio; //array de tipos precio

            $subtotal = 0;
            $detalles = [];

            foreach ($productos as $i => $productoId) {
                $cantidad = (int)($cantidades[$i] ?? 1);
                $precio   = (float)($precios[$i] ?? 0);
                $tipoPrecio = $tiposPrecios[$i] ?? 'base';
                $lineaTotal = $cantidad * $precio;

                $subtotal += $lineaTotal;

                $detalles[] = [
                    'cotizacion_id'   => $cotizacion->id,
                    'producto_id'     => $productoId,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precio,
                    'tipo_precio'     => $tipoPrecio,
                    'total'           => $lineaTotal, //aquí usas tu campo real
                ];
            }

            //Ajusta el cálculo de impuestos a tu lógica real (ejemplo IVA 16%)
           /* $impuestos = $subtotal * 0.16;
            $total     = $subtotal + $impuestos; */

            $total = $subtotal;

            // Actualizar la cotización
            $cotizacion->update([
                'cliente_id' => $request->cliente_id,
                'subtotal'   => $subtotal,
                /* 'impuestos'  => $impuestos, */
                'total'      => $total,
                'nota'          => $request->nota,
                'vigencia_dias' => $request->vigencia_dias ?? $cotizacion->vigencia_dias,
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
     * Cancelar cotización
    */
    public function destroy($id){
        try {
            $cotizacion = Cotizacion::findOrFail($id);

            if ($cotizacion->estado !== 'pendiente') {
                return response()->json([
                    'error' => 'Solo se pueden cancelar cotizaciones pendientes.'
                ], 400);
            }

            $cotizacion->update(['estado' => 'cancelada']);

            return response()->json([
                'success' => true,
                'message' => 'Cotización cancelada correctamente.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error cancelando cotización', [
                'cotizacion_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Error al cancelar la cotización: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar pagos según método
     */
    private function registrarPagos(Request $request, Venta $venta): void
    {
        // ===GUARDAR PAGOS SEGÚN MÉTODO ===
        $metodo = $request->input('metodo_pago');
        $referencia = $request->input('referencia_pago');

        switch ($metodo) {
            case 'efectivo':
                Pago::create([
                    'venta_id' => $venta->id,
                    'monto' => $request->input('monto_recibido'),
                    'metodo_pago' => 'efectivo',
                ]);
                break;

            case 'tarjeta':
            case 'transferencia':
                Pago::create([
                    'venta_id' => $venta->id,
                    'monto' => $venta->total_venta,
                    'metodo_pago' => $metodo,
                    'referencia' => $referencia,
                ]);
                break;

            case 'mixto':
                //EFECTIVO
                if ($request->filled('monto_efectivo') && $request->monto_efectivo > 0) {
                    Pago::create([
                        'venta_id' => $venta->id,
                        'monto' => $request->monto_efectivo,
                        'metodo_pago' => 'efectivo',
                    ]);
                }

                //Tarjeta
                if ($request->filled('monto_tarjeta') && $request->monto_tarjeta > 0) {
                    Pago::create([
                        'venta_id' => $venta->id,
                        'monto' => $request->monto_tarjeta,
                        'metodo_pago' => 'tarjeta',
                        'referencia' => $referencia,
                    ]);
                }
                break;
        }
    }


    public function descargarPdf($id){
        //Eager loading más específico
        $cotizacion = Cotizacion::with([
            'cliente:id,nombre,apellido,rfc,correo,telefono',
            'detalles.producto:id,codigo,nombre',
            'usuario:id,name'
        ])->findOrFail($id);

        $empresa = Empresa::first(); // o datos fijos

        if (!$empresa) {
            abort(404, 'No se encontró información de la empresa');
        }

        // Convertir imagen a base64 para DomPDF
        $logoBase64 = null;
        if ($empresa->imagen) {
            $imagePath = storage_path('app/public/' . $empresa->imagen);
            if (file_exists($imagePath)) {
                $imageData = file_get_contents($imagePath);
                $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
                $logoBase64 = 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
            }
        }

        $nota = 'Precios Sujetos a cambios sin previo aviso.  Esta cotización es válida por 30 días a partir de la fecha de emisión.';

        // Contenido para el QR con datos del cliente y datos de empresa
        $qrContenido = "BOLETA DE COTIZACION\n";
        $qrContenido .= "Empresa: {$cotizacion->razon_social_empresa}\n";
        $qrContenido .= "RFC Empresa: {$cotizacion->rfc_empresa}\n";
        $qrContenido .= "Cliente: {$cotizacion->nombre_cliente}\n";
        $qrContenido .= "RFC/CURP: {$cotizacion->rfc_cliente}\n";
        $qrContenido .= "Total: $" . number_format($cotizacion->total_venta, 2) . "\n";
        $qrContenido .= "Folio: {$cotizacion->folio}\n";
        $qrContenido .= "Validación: {$cotizacion->razon_social_empresa}";

        // Generar QR
        /* $qr = base64_encode(QrCode::format('png')->size(150)->generate($qrContenido)); */
        // Intentar generar el QR (compatibilidad local + AlwaysData)
        try {
            // En local: usa el método normal de Simple QrCode
            $qr = base64_encode(
                QrCode::format('png')
                    ->size(100)
                    ->generate('http://sistemaventas2025.test/boleta/'.$cotizacion->id)
            );
        } catch (\Exception $e) {
            // Generar QR como SVG (compatible con AlwaysData)
            $renderer = new ImageRenderer(
                new RendererStyle(100),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);

            $qr = base64_encode($writer->writeString($qrContenido));
        }

        $pdf = Pdf::loadView('modulos.cotizaciones.pdf', compact('cotizacion', 'empresa', 'logoBase64','qr', 'nota'))
            ->setPaper('A4', 'portrait')
            ->setOption('enable-local-file-access', true); // Para imágenes locales

        // Nombre de archivo más descriptivo
        /* $nombreArchivo = sprintf(
            'cotizacion_%s_%s.pdf',
            $folio,
            now()->format('Ymd')
        ); */

        return $pdf->stream('cotizacion_'.$cotizacion->id.'.pdf');
    }
}
