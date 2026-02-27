<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleCompra extends Model
{
    use HasFactory;

    protected $fillable = [
        'compra_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relaciones
    public function compra(){
        return $this->belongsTo(Compra::class);
    }

    public function producto(){
        return $this->belongsTo(Producto::class);
    }

    public function proveedor(){
        return $this->belongsTo(Proveedor::class);
    }


    // Eventos del modelo
    protected static function boot(){
        parent::boot();

        static::saving(function ($detalle) {
            $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
        });

        static::saved(function ($detalle) {
            $detalle->compra->calcularTotales();
        });

        static::deleted(function ($detalle) {
            $detalle->compra->calcularTotales();
        });
    }
}
