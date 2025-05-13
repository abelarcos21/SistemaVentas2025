<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //crear 5 productos
        Producto::factory()->count(5)->create();

        //asignar un usuario/proveedor/categoría ya existentes:
        /* Producto::factory()->count(10)->create([
            'user_id' => 1,
            'categoria_id' => 2,
            'proveedor_id' => 3,
        ]); */
    }
}
