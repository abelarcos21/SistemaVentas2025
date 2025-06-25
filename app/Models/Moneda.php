<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
