<?php

namespace App\Http\Controllers\Facturacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FacturaCenterService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\FormaPago;
use App\Models\MetodoPago;
use App\Models\UsoCfdi;
use App\Models\RegimenFiscal;
use App\Models\ObjetoImpuesto;

class FacturaController extends Controller
{
    //
    public function create(){
        return view('modulos.facturas.create', [

            'formas_pago' => FormaPago::where('activo', true)->pluck('descripcion', 'clave'),
            'metodos_pago' => MetodoPago::where('activo', true)->pluck('descripcion', 'clave'),
            'usos_cfdi' => UsoCfdi::where('activo', true)->pluck('descripcion', 'clave'),
            'regimenes_fiscales' => RegimenFiscal::where('activo', true)->pluck('descripcion', 'clave'),
            'objetos_impuesto' => ObjetoImpuesto::where('activo', true)->pluck('descripcion', 'clave'),

        ]);
    }

    public function index(){
        //$facturas = Factura::all();
        return view('modulos.facturas.index');
    }

    public function timbrar(Request $request, FacturaCenterService $fc){

        $datos = $this->generarDatosCfdi($request); // Prepara(arma) el JSON CFDI 4.0 de la factura

        $respuesta = $fc->timbrarFactura($datos);

        if($respuesta['ok']){

            $uuid = $respuesta['uuid'];
            $pdfBase64 = $respuesta['pdf']; // viene como base64 desde Factura Center
            $xmlBase64 = $respuesta['xml'];

            $nombreBase = Str::slug($request->razon_social) . '_' . $uuid;

            // Guardar PDF
            if ($pdfBase64) {
                Storage::put("facturas/{$nombreBase}.pdf", base64_decode($pdfBase64));
            }

            // Guardar XML (opcional)
            if ($xmlBase64) {
                Storage::put("facturas/{$nombreBase}.xml", base64_decode($xmlBase64));
            }

            return view('factura_exitosa', [
                'uuid' => $uuid,
                'archivo_pdf' => "{$nombreBase}.pdf",
                'archivo_xml' => "{$nombreBase}.xml"
            ]);
            // Guardar info en base de datos, mostrar PDF/descarga
            // $this->guardarFacturaEnBD($respuesta);

            /* return response()->json([
                'ok' => true,
                'uuid' => $uuid,
                'mensaje' => 'Factura timbrada correctamente.',
                'archivo_pdf' => "storage/facturas/{$nombreBase}.pdf",
                'archivo_xml' => "storage/facturas/{$nombreBase}.xml"
            ]); */
        }

        //return response()->json($respuesta);
        return back()->with('error', $respuesta['mensaje']);

        /* Después de timbrar:

        PDF en: storage/app/facturas/nombre_cliente_uuid.pdf

        XML en: storage/app/facturas/nombre_cliente_uuid.xml */

        //Factura Center puede devolverte:

        /* xml_base64: el XML timbrado

        pdf_base64: el PDF generado

        Solo tienes que decodificarlos: */
        /* file_put_contents(storage_path('factura.pdf'), base64_decode($respuesta['pdf_base64']));
        file_put_contents(storage_path('factura.xml'), base64_decode($respuesta['xml_base64'])); */
    }

    private function generarDatosCfdi(Request $request): array {

        $cantidad = $request->cantidad;
        $precio = $request->precio;
        $importe = round($cantidad * $precio, 2);
        $iva = round($importe * 0.16, 2);

        return [
            'rfc_emisor' => env('FC_RFC'),
            'receptor' => [
                'rfc' => $request->input('rfc'),
                'nombre' => $request->input('razon_social'),
                'uso_cfdi' => $request->input('uso_cfdi') ?? 'G03',
            ],
            'conceptos' => [
                [
                    'clave_prod_serv' => $request->input('clave_producto'),
                    'cantidad' => $request->input('cantidad'),
                    'unidad' => $request->input('unidad'),
                    'descripcion' => $request->input('descripcion'),
                    'valor_unitario' => $request->input('precio'),
                    'importe' => $request->input('cantidad') * $request->input('precio'),
                    'impuestos' => [
                        'traslados' => [
                            [
                                'base' => $request->input('cantidad') * $request->input('precio'),
                                'impuesto' => '002',
                                'tipo_factor' => 'Tasa',
                                'tasa_o_cuota' => 0.16,
                                'importe' => ($request->input('cantidad') * $request->input('precio')) * 0.16,
                            ],
                        ]
                    ]
                ]
            ],
            'forma_pago' => $request->input('forma_pago') ?? '01',
            'metodo_pago' => $request->input('metodo_pago') ?? 'PUE',
            'moneda' => 'MXN',
            'tipo_comprobante' => 'I',
            'lugar_expedicion' => $request->input('cp') ?? '01000',
        ];

        //convierte los datos del formulario en un array compatible con CFDI 4.0, listo para enviar a Factura Center:

        /* $cantidad = $request->cantidad;
        $precio = $request->precio;
        $importe = round($cantidad * $precio, 2);
        $iva = round($importe * 0.16, 2);
        $total = round($importe + $iva, 2); */

        /* return [

            "tipo" => "factura",
            "serie" => $request->serie,
            "folio" => $request->folio,
            "fecha" => $request->fecha,
            "forma_pago" => $request->forma_pago,
            "metodo_pago" => $request->metodo_pago,
            "moneda" => $request->moneda,
            "exportacion" => $request->exportacion,
            "uso_cfdi" => $request->uso_cfdi,
            "tipo_comprobante" => "I",

            "receptor" => [
                "rfc" => strtoupper($request->rfc),
                "nombre" => strtoupper($request->razon_social),
                "codigo_postal" => $request->cp,
                "regimen_fiscal" => $request->regimen_fiscal,
                "uso_cfdi" => $request->uso_cfdi,
            ],

            "conceptos" => [
                [
                    "clave_prod_serv" => $request->clave_producto,
                    "cantidad" => $cantidad,
                    "clave_unidad" => $request->unidad,
                    "descripcion" => $request->descripcion,
                    "valor_unitario" => $precio,
                    "importe" => $importe,
                    "objeto_impuesto" => $request->objeto_impuesto,

                    "impuestos" => [
                        "traslados" => [
                            [
                                "base" => $importe,
                                "impuesto" => "002",          // IVA
                                "tipo_factor" => "Tasa",
                                "tasa" => 0.160000,
                                "importe" => $iva
                            ]
                        ]
                    ]
                ]
            ],

            "condiciones_pago" => "Contado",
        ];
       */
    }
}
