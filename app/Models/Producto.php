<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    //
    use HasFactory;

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

   /*  public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    } */
}
