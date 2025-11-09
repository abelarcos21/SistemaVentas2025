<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{

    protected $fillable = ['user_id', 'cliente_id', 'empresa_id', 'caja_id', 'cotizacion_id', 'total_venta', 'estado', 'folio'];

    public function caja(){
        return $this->belongsTo(Caja::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function pagos(){
        return $this->hasMany(Pago::class);
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }

    public function detalles(){
        return $this->hasMany(DetalleVenta::class);
    }

    public function cotizacion(){
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id');
    }
}
