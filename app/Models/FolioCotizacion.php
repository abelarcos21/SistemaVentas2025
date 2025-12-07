<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FolioCotizacion extends Model
{
    //
    protected $table = 'folio_cotizaciones';

    protected $fillable = ['serie', 'ultimo_numero'];

    /**
    * Generar el siguiente folio
    */
    public static function generarSiguiente(string $serie = 'COT'): string
    {
        $folio = self::lockForUpdate()->firstOrCreate(
            ['serie' => $serie],
            ['ultimo_numero' => 0]
        );

        $folio->ultimo_numero += 1;
        $folio->save();

        return sprintf('%s-%06d', $folio->serie, $folio->ultimo_numero);
    }
}
