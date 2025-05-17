<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    //

    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'rfc',
        'telefono',
        'correo',
        'activo' //no es necesario si no se llena manualmente/si es nesesario se llena en el formulario de creacion y edit
    ];

}
