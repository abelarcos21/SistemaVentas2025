<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    use HasFactory;

    protected $table = 'unidades';

    protected $fillable = [
        'nombre',
        'abreviatura',
        'activo'
    ];

    // Relación inversa (opcional, pero útil)
    public function productos(){
        return $this->hasMany(Producto::class);
    }
}
