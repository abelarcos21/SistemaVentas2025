<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{

    protected $fillable = ['user_id', 'cliente_id', 'total_venta', 'estado', 'folio'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
