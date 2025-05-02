<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    //
    //
    protected $table = 'productos';


    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'codigo_postal',
        'sitio_web',
        'notas',
    ];
}
