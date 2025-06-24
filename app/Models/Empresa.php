<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    /** @use HasFactory<\Database\Factories\EmpresaFactory> */
    use HasFactory;


    protected $fillable = [

        'razon_social',
        'rfc',
        'telefono',
        'correo',
        'moneda',
        'imagen',
        'direccion',
        'regimen_fiscal',
        'codigo_postal',
    ];
}
