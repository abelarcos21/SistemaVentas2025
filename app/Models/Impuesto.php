<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{
    //
    protected $fillable = [
        'nombre', 'impuesto', 'tipo', 'factor', 'tasa'
    ];
}
