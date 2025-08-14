<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Compra extends Model
{
    //

    use HasFactory;

    protected $table = 'compras';

    public function user(){ //para acceder al campo user_id
        return $this->belongsTo(User::class);
    }

    public function producto(){ //para acceder al campo producto_id
        return $this->belongsTo(Producto::class);
    }
}
