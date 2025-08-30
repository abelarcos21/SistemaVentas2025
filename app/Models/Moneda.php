<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Moneda extends Model
{
    //
    protected $table = 'monedas';

    protected $fillable = [
        'codigo', 'nombre', 'simbolo',
    ];

    public function empresas(){
        return $this->hasMany(Empresa::class, 'moneda', 'codigo');
    }

    public function productos(): HasMany{
        return $this->hasMany(Producto::class, 'moneda_id', 'id');
    }
}
