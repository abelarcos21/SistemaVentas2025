<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ObjetoImpuesto extends Model
{
    //
    use HasFactory;
    protected $table = 'objetos_impuesto'; // Asegura que apunta a la tabla correcta
    protected $fillable = ['clave', 'descripcion', 'activo'];
}
