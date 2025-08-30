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
            MonedaSeeder::class,//primero el de moneda para no dar error en productoseeder
            ProductoSeeder::class,
            UsuarioSeeder::class,
            ImagenSeeder::class,
            ClienteSeeder::class,
            EmpresaSeeder::class,
            CatalogosSatSeeder::class,
            PermissionTableSeeder::class,
            CreateAdminUserSeeder::class,
        ]);
    }
}
