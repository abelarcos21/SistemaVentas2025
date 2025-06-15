<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormaPago extends Model
{

    use HasFactory;
    //
    protected $table = 'formas_pago'; // Asegura que apunta a la tabla correcta
    //public $timestamps = true; // O false, según tu tabla
    protected $fillable = ['clave', 'descripcion', 'activo'];
}
