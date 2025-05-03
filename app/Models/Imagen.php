<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    //
    protected $table = 'imagens';

    protected $fillable = [
        'producto_id',
        'nombre',
        'ruta',
    ];
}
