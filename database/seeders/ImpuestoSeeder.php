<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImpuestoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('impuestos')->insert([
            // Traslados
            ['clave' => '002', 'nombre' => 'IVA 16%', 'tipo' => 'traslado', 'tasa' => 0.1600, 'activo' => true],
            ['clave' => '002', 'nombre' => 'IVA 8%', 'tipo' => 'traslado', 'tasa' => 0.0800, 'activo' => true],
            ['clave' => '003', 'nombre' => 'IEPS 8%', 'tipo' => 'traslado', 'tasa' => 0.0800, 'activo' => true],

            // Retenciones
            ['clave' => '001', 'nombre' => 'ISR 10%', 'tipo' => 'retencion', 'tasa' => 0.1000, 'activo' => true],
            ['clave' => '002', 'nombre' => 'IVA 10.66667% (Retención)', 'tipo' => 'retencion', 'tasa' => 0.106667, 'activo' => true],
            ['clave' => '002', 'nombre' => 'IVA 6%', 'tipo' => 'retencion', 'tasa' => 0.0600, 'activo' => true],
        ]);
    }
}
