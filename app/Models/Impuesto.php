<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{
    protected $table = 'impuestos'; // Asegura que apunta a la tabla correcta
    /* protected $fillable = [
        'nombre', 'impuesto', 'tipo', 'factor', 'tasa'
    ]; */

    protected $fillable = [
        'clave',
        'nombre',
        'tipo',
        'tasa',
        'activo',
    ];
}
