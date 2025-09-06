<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
    //

    use HasFactory;

    protected $table = 'proveedores';


    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'codigo_postal',
        'sitio_web',
        'notas',
        'activo' //no es necesario si no se llena manualmente/si es nesesario se llena en el formulario de creacion y edit
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
