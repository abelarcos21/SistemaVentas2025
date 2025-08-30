<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Empresa extends Model
{
    /** @use HasFactory<\Database\Factories\EmpresaFactory> */
    use HasFactory;


    protected $fillable = [

        'razon_social',
        'rfc',
        'telefono',
        'correo',
        'moneda_id',
        'imagen',
        'direccion',
        'regimen_fiscal',
        'codigo_postal',
    ];

    public function moneda(){
        return $this->belongsTo(Moneda::class, 'moneda_id', 'codigo');
    }

    // Helper para obtener código de moneda
    public function getCodigoMonedaAttribute(): string {
        return $this->moneda?->codigo ?? 'MXN';
    }

    // Helper para obtener nombre de moneda
    public function getNombreMonedaAttribute(): string {
        return $this->moneda?->nombre ?? 'Peso Mexicano';
    }
}
