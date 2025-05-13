<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Imagen;
use App\Models\Producto;

class ImagenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Si ya tienes productos en la BD
        $producto = Producto::first();
        Imagen::factory()->count(3)->create([
            'producto_id' => $producto->id,
        ]);
    }
}
