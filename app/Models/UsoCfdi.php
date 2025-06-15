<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsoCfdi extends Model
{
    //
    use Hasfactory;
    protected $table = 'usos_cfdi'; // Asegura que apunta a la tabla correcta
    protected $fillable = ['clave', 'descripcion', 'activo'];
}
