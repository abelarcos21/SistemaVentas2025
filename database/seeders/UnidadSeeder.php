<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unidad;

class UnidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unidades = [
            // PESO
            [
                'nombre' => 'Kilogramo',
                'abreviatura' => 'kg',
                'codigo_sat' => 'KGM',
                'tipo' => 'peso',
                'factor_conversion' => 1000,
                'unidad_base' => 'gramo',
                'permite_decimales' => true,
                'activo' => true,
                'descripcion' => 'Unidad de masa del sistema internacional',
            ],
            [
                'nombre' => 'Gramo',
                'abreviatura' => 'g',
                'codigo_sat' => 'GRM',
                'tipo' => 'peso',
                'factor_conversion' => 1,
                'unidad_base' => 'gramo',
                'permite_decimales' => true,
                'activo' => true,
                'descripcion' => 'Unidad base de masa',
            ],
            [
                'nombre' => 'Libra',
                'abreviatura' => 'lb',
                'codigo_sat' => 'LBR',
                'tipo' => 'peso',
                'factor_conversion' => 453.592,
                'unidad_base' => 'gramo',
                'permite_decimales' => true,
                'activo' => true,
                'descripcion' => 'Unidad de peso anglosajona',
            ],
            [
                'nombre' => 'Onza',
                'abreviatura' => 'oz',
                'codigo_sat' => 'ONZ',
                'tipo' => 'peso',
                'factor_conversion' => 28.3495,
                'unidad_base' => 'gramo',
                'permite_decimales' => true,
                'activo' => true,
                'descripcion' => 'Unidad de peso equivalente a 1/16 de libra',
            ],

            // VOLUMEN
            [
                'nombre' => 'Litro',
                'abreviatura' => 'lt',
                'codigo_sat' => 'LTR',
                'tipo' => 'volumen',
                'factor_conversion' => 1000,
                'unidad_base' => 'mililitro',
                'permite_decimales' => true,
                'activo' => true,
                'descripcion' => 'Unidad de volumen del sistema métrico',
            ],
            [
                'nombre' => 'Mililitro',
                'abreviatura' => 'ml',
                'codigo_sat' => 'MLT',
                'tipo' => 'volumen',
                'factor_conversion' => 1,
                'unidad_base' => 'mililitro',
                'permite_decimales' => true,
                'activo' => true,
                'descripcion' => 'Unidad base de volumen',
            ],
            [
                'nombre' => 'Galón',
                'abreviatura' => 'gal',
                'codigo_sat' => 'GLI',
                'tipo' => 'volumen',
                'factor_conversion' => 3785.41,
                'unidad_base' => 'mililitro',
                'permite_decimales' => true,
                'activo' => true,
                'descripcion' => 'Unidad de volumen estadounidense',
            ],

            // LONGITUD
            [
                'nombre' => 'Metro',
                'abreviatura' => 'm',
                'codigo_sat' => 'MTR',
                'tipo' => 'longitud',
                'factor_conversion' => 100,
                'unidad_base' => 'centímetro',
                'permite_decimales' => true,
                'activo' => true,
                'descripcion' => 'Unidad de longitud del sistema internacional',
            ],
            [
                'nombre' => 'Centímetro',
                'abreviatura' => 'cm',
                'codigo_sat' => 'CMT',
                'tipo' => 'longitud',
                'factor_conversion' => 1,
                'unidad_base' => 'centímetro',
                'permite_decimales' => true,
                'activo' => true,
                'descripcion' => 'Unidad base de longitud',
            ],

            // PIEZA (Las más comunes)
            [
                'nombre' => 'Pieza',
                'abreviatura' => 'pza',
                'codigo_sat' => 'H87',
                'tipo' => 'pieza',
                'factor_conversion' => null,
                'unidad_base' => null,
                'permite_decimales' => false,
                'activo' => true,
                'descripcion' => 'Unidad individual de producto',
            ],
            [
                'nombre' => 'Paquete',
                'abreviatura' => 'paq',
                'codigo_sat' => 'E54',
                'tipo' => 'pieza',
                'factor_conversion' => null,
                'unidad_base' => null,
                'permite_decimales' => false,
                'activo' => true,
                'descripcion' => 'Conjunto de piezas empaquetadas',
            ],
            [
                'nombre' => 'Caja',
                'abreviatura' => 'cja',
                'codigo_sat' => 'XBX',
                'tipo' => 'pieza',
                'factor_conversion' => null,
                'unidad_base' => null,
                'permite_decimales' => false,
                'activo' => true,
                'descripcion' => 'Contenedor de productos',
            ],
            [
                'nombre' => 'Par',
                'abreviatura' => 'par',
                'codigo_sat' => 'PR',
                'tipo' => 'pieza',
                'factor_conversion' => 2,
                'unidad_base' => 'pieza',
                'permite_decimales' => false,
                'activo' => true,
                'descripcion' => 'Conjunto de dos piezas',
            ],
            [
                'nombre' => 'Docena',
                'abreviatura' => 'dz',
                'codigo_sat' => 'DZN',
                'tipo' => 'pieza',
                'factor_conversion' => 12,
                'unidad_base' => 'pieza',
                'permite_decimales' => false,
                'activo' => true,
                'descripcion' => 'Conjunto de 12 piezas',
            ],

            // TIEMPO (para servicios)
            [
                'nombre' => 'Hora',
                'abreviatura' => 'hr',
                'codigo_sat' => 'HUR',
                'tipo' => 'tiempo',
                'factor_conversion' => 60,
                'unidad_base' => 'minuto',
                'permite_decimales' => true,
                'activo' => true,
                'descripcion' => 'Unidad de tiempo para servicios',
            ],
            [
                'nombre' => 'Día',
                'abreviatura' => 'día',
                'codigo_sat' => 'DAY',
                'tipo' => 'tiempo',
                'factor_conversion' => 1440,
                'unidad_base' => 'minuto',
                'permite_decimales' => false,
                'activo' => true,
                'descripcion' => 'Unidad de tiempo equivalente a 24 horas',
            ],

            // OTROS
            [
                'nombre' => 'Servicio',
                'abreviatura' => 'srv',
                'codigo_sat' => 'E48',
                'tipo' => 'otro',
                'factor_conversion' => null,
                'unidad_base' => null,
                'permite_decimales' => false,
                'activo' => true,
                'descripcion' => 'Unidad para servicios intangibles',
            ],
            [
                'nombre' => 'Actividad',
                'abreviatura' => 'act',
                'codigo_sat' => 'ACT',
                'tipo' => 'otro',
                'factor_conversion' => null,
                'unidad_base' => null,
                'permite_decimales' => false,
                'activo' => true,
                'descripcion' => 'Unidad para actividades o eventos',
            ],
        ];

        foreach ($unidades as $unidad) {
            Unidad::firstOrCreate(
                ['abreviatura' => $unidad['abreviatura']], // Busca por abreviatura
                $unidad // Si no existe, crea con estos datos
            );
        }
    }
}
