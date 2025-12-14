<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Cotizacion extends Model
{
    //
    protected $table = 'cotizaciones';

    protected $fillable = [
        'folio', 'cliente_id', 'user_id', 'fecha', 'subtotal', 'impuestos', 'total', 'estado','nota','vigencia_dias'
    ];

    protected $casts = [
        'fecha' => 'date',
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
        'vigencia_dias' => 'integer',
    ];

    //RELACIONES
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

    //MÉTODOS ÚTILES
    /**
     * Verificar si la cotización está vigente
     */
    public function estaVigente(): bool
    {
        if ($this->estado !== 'pendiente') {
            return false;
        }

        return now()->lte($this->fecha_vencimiento);
    }

    /**
     * Obtener fecha de vencimiento
     */
    public function getFechaVencimientoAttribute(): Carbon
    {
        return Carbon::parse($this->fecha)->addDays($this->vigencia_dias);
    }

    /**
     * Obtener días restantes de vigencia
     */
    public function getDiasRestantesAttribute(): int
    {
        if ($this->estado !== 'pendiente') {
            return 0;
        }

        $dias = now()->diffInDays($this->fecha_vencimiento, false);
        return (int)$dias;
    }

    /**
     * Scope para cotizaciones vencidas
     */
    public function scopeVencidas($query)
    {
        return $query->where('estado', 'pendiente')
            ->whereRaw('DATE_ADD(fecha, INTERVAL vigencia_dias DAY) < NOW()');
    }

    /**
     * Scope para cotizaciones vigentes
     */
    public function scopeVigentes($query)
    {
        return $query->where('estado', 'pendiente')
            ->whereRaw('DATE_ADD(fecha, INTERVAL vigencia_dias DAY) >= NOW()');
    }
}
