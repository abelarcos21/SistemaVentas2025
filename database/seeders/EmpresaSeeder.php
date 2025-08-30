<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\Moneda;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Asegurar que existe la moneda MXN
        $monedaMxn = Moneda::where('codigo', 'MXN')->first();

        if (!$monedaMxn) {
            $this->command->warn('La moneda MXN no existe. Ejecuta MonedasSeeder primero.');
            return;
        }

        // OPCIÓN 1: Usando el factory con el helper
        Empresa::factory()->mexicana()->create();

        // OPCIÓN 2: Creación directa (como lo tenías antes)
        // Empresa::factory()->create([
        //     'razon_social' => 'Mi Empresa S.A. de C.V.',
        //     'rfc' => 'ABC123456T12',
        //     'moneda_id' => $monedaMxn->id, // Cambio principal aquí
        // ]);

        // OPCIÓN 3: Crear múltiples empresas con diferentes monedas
        // $monedasDisponibles = ['MXN', 'USD', 'EUR'];
        //
        // foreach ($monedasDisponibles as $codigo) {
        //     Empresa::factory()
        //         ->conMoneda($codigo)
        //         ->create();
        // }
    }
}
