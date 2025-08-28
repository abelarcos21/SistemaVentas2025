<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovimientoCaja extends Model
{
    //
    use HasFactory;

    protected $table = 'movimientos_caja';

    protected $fillable = [
        'caja_id', 'tipo', 'monto', 'descripcion'
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
}
