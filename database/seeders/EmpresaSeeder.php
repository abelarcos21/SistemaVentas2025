<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Empresa;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        /* Empresa::factory()->count(1)->create(); */

        Empresa::factory()->create([
            'razon_social' => 'Mi Empresa S.A. de C.V.',
            'rfc' => 'ABC123456T12',
            'moneda' => 'MXN',
        ]);
    }
}
