<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MetodoPago extends Model
{
    //
    use HasFactory;
    protected $table = 'metodos_pago'; // Asegura que apunta a la tabla correcta
    protected $fillable = ['clave', 'descripcion', 'activo'];
}
