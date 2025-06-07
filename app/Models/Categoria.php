<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function productos(){
        return $this->hasMany(Producto::class);
    }
}
