<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RegimenFiscal extends Model
{
    //
    use HasFactory;
    protected $table = 'regimenes_fiscales'; // Asegura que apunta a la tabla correcta
    protected $fillable = [
        'clave',
        'descripcion',
        'persona_fisica',
        'persona_moral',
        'activo',
    ];
}
