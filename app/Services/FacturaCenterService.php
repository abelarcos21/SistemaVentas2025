<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FacturaCenterService{

    protected $url;
    protected $user;
    protected $password;
    protected $rfc;
    protected $token;

    public function __construct(){
        //$this->url = config('services.facturacenter.url');
        $this->url = 'https://api.facturacenter.com/v1/crear'; // Endpoint de timbrado
        $this->user = config('services.facturacenter.user');
        $this->password = config('services.facturacenter.password');
        $this->rfc = config('services.facturacenter.rfc');
        $this->token = env('FACTURACENTER_TOKEN'); // TOKEN desde .env
    }

    public function timbrarFactura(array $data){
        $response = Http::withBasicAuth($this->user, $this->password)
            ->post($this->url . '/cfdi40/stamp', [
                'rfc_emisor' => $this->rfc,
                'cadena'     => base64_encode($data['xml']), // o directamente el XML
                'codigo'     => '123456', // código de integración
                'generar_pdf' => true,
            ]);

        return $response->json();

    }

    //listo para integrarse en Laravel 11 y trabajar con la
    // API REST del PAC Factura Center (modo timbrado CFDI 4.0).
    public function timbrar(array $datos){
        $response = Http::withToken($this->token)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post($this->url, $datos);

        if ($response->successful()) {
            return [
                'ok' => true,
                'uuid' => $response->json('uuid') ?? null,
                'pdf' => $response->json('pdf') ?? null,
                'xml' => $response->json('xml') ?? null,
                'data' => $response->json()
            ];
        } else {
            return [
                'ok' => false,
                'mensaje' => $response->json('message') ?? 'Error desconocido al timbrar.',
                'status' => $response->status()
            ];
        }
    }

}
