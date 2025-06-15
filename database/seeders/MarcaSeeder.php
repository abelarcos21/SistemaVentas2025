<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Marca;
use Faker\Factory as Faker;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Resetear el generador único
        $faker = Faker::create();
        $faker->unique(true);

        // Ejecutar factory
        Marca::factory()->count(5)->create();

    }
}
