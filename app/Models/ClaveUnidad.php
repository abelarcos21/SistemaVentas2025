<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClaveUnidad extends Model
{
    //
    use HasFactory;

    protected $table = 'claves_unidad';

    protected $fillable = [
        'clave',
        'nombre',
        'activo',
    ];
}
