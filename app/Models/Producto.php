<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    //
    //
    protected $table = 'productos';


    protected $fillable = [
        'user_id',
        'categoria_id',
        'proveedor_id',
        'codigo',
        'nombre',
        'descripcion',
        'precio_venta'

    ];

    //RELACION PARA ACCEDER ALA IMAGEN
    public function imagen(){
        return $this->hasOne(Imagen::class);
    }
}
