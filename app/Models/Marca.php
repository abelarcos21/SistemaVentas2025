<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;//uso de la relacion

class Marca extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo', //no es necesario si no se llena manualmente/si es nesesario se llena en el formulario de creacion y edit
    ];

    public function productos(): HasMany{
        return $this->hasMany(Producto::class);
    }
}
