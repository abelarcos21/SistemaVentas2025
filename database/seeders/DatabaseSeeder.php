<?php

namespace Database\Seeders;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;

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
            MarcaSeeder::class,
            ProductoSeeder::class,
            UsuarioSeeder::class,
            ImagenSeeder::class,
            ClienteSeeder::class,
            MonedaSeeder::class,
            EmpresaSeeder::class,
            CatalogosSatSeeder::class,
            PermissionTableSeeder::class,
            CreateAdminUserSeeder::class,
        ]);
    }
}
