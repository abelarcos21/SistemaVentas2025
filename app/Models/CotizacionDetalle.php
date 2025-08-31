<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionDetalle extends Model
{
    //

    protected $table = 'cotizacion_detalles';

    protected $fillable = [
        'cotizacion_id', 'producto_id', 'cantidad', 'precio_unitario', 'total'
    ];

    public function producto(){
        return $this->belongsTo(Producto::class);
    }
}
