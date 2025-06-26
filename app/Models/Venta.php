<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{

    protected $fillable = ['user_id', 'cliente_id', 'total_venta', 'estado', 'folio'];

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
}
