<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Folio extends Model
{
    //
    use HasFactory;
    protected $table = 'folios';
    protected $fillable = ['serie', 'ultimo_numero'];
}
