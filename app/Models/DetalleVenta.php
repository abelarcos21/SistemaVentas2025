<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleVenta extends Model
{
    //
    use HasFactory;
    protected $table = 'detalle_venta';

    public function producto(){
        return $this->belongsTo(Producto::class);
    }
}
