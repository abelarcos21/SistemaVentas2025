<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Caja extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'user_id', 'monto_inicial', 'monto_final',
        'total_ventas', 'total_ingresos', 'total_egresos',
        'diferencia', 'apertura', 'cierre', 'estado'
    ];


    public function scopeAbierta($query){//scope para obtener la caja activa
        return $query->where('estado', 'abierta');
    }

    public static function getCajaActivaByUser($userId){ //helper para el usuario
        return self::where('user_id', $userId)
                ->where('estado', 'abierta')
                ->first();
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function movimientos(){
        return $this->hasMany(MovimientoCaja::class);
    }

    public function ventas(){
        return $this->hasMany(Venta::class);
    }
}
