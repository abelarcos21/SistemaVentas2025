<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Compra extends Model
{
    //

    use HasFactory, SoftDeletes;

    protected $table = 'compras';

    protected $fillable = [
        'user_id',
        'proveedor_id',
        'numero_factura',
        'fecha_compra',
        'metodo_pago',
        'subtotal',
        'descuento',
        'impuesto',
        'total',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'subtotal' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relaciones
    public function user(){//para acceder al campo user_id
        return $this->belongsTo(User::class);
    }

    public function proveedor(){
        return $this->belongsTo(Proveedor::class);
    }

    public function detalles(){
        return $this->hasMany(DetalleCompra::class);
    }

    // Métodos auxiliares
    public function calcularTotales(){
        $this->subtotal = $this->detalles->sum('subtotal');
        $this->total = $this->subtotal + $this->impuesto;
        $this->save();
    }

    public function isPendiente(){
        return $this->estado === 'pendiente';
    }

    public function completar(){
        $this->estado = 'completada';
        $this->save();

        // Aquí puedes agregar lógica para actualizar inventario
    }
}
