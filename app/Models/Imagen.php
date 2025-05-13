<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Imagen extends Model
{
    //
    use HasFactory;

    protected $table = 'imagens';

    protected $fillable = [
        'producto_id',
        'nombre',
        'ruta',
    ];
}
