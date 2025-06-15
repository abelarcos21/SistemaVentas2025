<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClaveProdServ extends Model
{
    //
    use HasFactory;

    protected $table = 'claves_prod_serv';
    
    protected $fillable = [
        'clave',
        'descripcion',
        'activo',
    ];
}
