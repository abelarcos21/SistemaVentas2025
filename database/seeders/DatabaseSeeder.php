<?php

namespace Database\Seeders;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Producto;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //todos los seeders
        $this->call([
            CategoriaSeeder::class,
            ProveedorSeeder::class,
            ProductoSeeder::class,
            UsuarioSeeder::class,
            ImagenSeeder::class,
        ]);
    }
}
