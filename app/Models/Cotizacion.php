<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    //
    protected $table = 'cotizaciones';

    protected $fillable = [
        'cliente_id', 'user_id', 'fecha', 'subtotal', 'impuestos', 'total', 'estado'
    ];

    public function detalles(){
        return $this->hasMany(CotizacionDetalle::class);
    }

    public function cliente() {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function venta(){
        return $this->hasOne(Venta::class, 'cotizacion_id');

    }
}
