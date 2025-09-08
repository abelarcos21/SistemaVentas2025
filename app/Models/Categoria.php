<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;//uso de la relacion

class Categoria extends Model
{
    //

    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = [
        'user_id',
        'nombre',
        'descripcion',
        'medida',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function productos(){
        return $this->hasMany(Producto::class);
    }

    /**
     *Relación con el usuario que creó la categoría
    */
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
