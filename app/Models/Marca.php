<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope para obtener solo marcas activas
    */
    public function scopeActivo($query){
        return $query->where('activo', true);
    }

    /**
     *Scope para obtener marcas por nombre
    */
    public function scopePorNombre($query, $nombre){
        return $query->where('nombre', 'like', "%{$nombre}%");
    }


    public function productos(): HasMany{
        return $this->hasMany(Producto::class);
    }
}
