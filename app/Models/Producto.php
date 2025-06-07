<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;//USO ACEDER ALA RELACION

class Producto extends Model
{
    //
    use HasFactory;

    protected $table = 'productos';


    protected $fillable = [
        'user_id',
        'categoria_id',
        'proveedor_id',
        'marca_id',
        'codigo',
        'barcode_path',
        'nombre',
        'descripcion',
        'precio_venta'

    ];

    //RELACION PARA ACCEDER ALA IMAGEN
    public function imagen(){
        return $this->hasOne(Imagen::class);
    }

    //RELACION PARA ACCEDER ALA MARCA
    public function marca(): BelongsTo{
        return $this->belongsTo(Marca::class);
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
