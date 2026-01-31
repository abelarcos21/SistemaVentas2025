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
            ['nombre' => 'Pieza', 'abreviatura' => 'pza'],
            ['nombre' => 'Kilogramo', 'abreviatura' => 'kg'],
            ['nombre' => 'Gramo', 'abreviatura' => 'gr'],
            ['nombre' => 'Litro', 'abreviatura' => 'lt'],
            ['nombre' => 'Mililitro', 'abreviatura' => 'ml'],
            ['nombre' => 'Metro', 'abreviatura' => 'm'],
            ['nombre' => 'Paquete', 'abreviatura' => 'pqt'],
            ['nombre' => 'Caja', 'abreviatura' => 'cja'],
            ['nombre' => 'Servicio', 'abreviatura' => 'srv'],
        ];

        foreach ($unidades as $unidad) {
            Unidad::firstOrCreate(
                ['abreviatura' => $unidad['abreviatura']], // Busca por abreviatura
                $unidad // Si no existe, crea con estos datos
            );
        }
    }
}
