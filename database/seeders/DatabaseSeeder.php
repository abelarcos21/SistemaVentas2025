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

        $this->command->info('🚀 Iniciando seeders...');
        $this->command->newLine();

        // 1. Primero crear roles y permisos
        $this->command->info('1️⃣  Creando roles y permisos...');
        $this->call(PermissionTableSeeder::class);
        $this->command->newLine();

        // 2. Luego crear usuarios
        $this->command->info('2️⃣  Creando usuarios...');
        $this->call(SuperAdminSeeder::class);
        $this->command->newLine();

        $this->command->info('✅ Seeders completados exitosamente!');
        $this->command->newLine();

        $this->command->warn('⚠️  IMPORTANTE: Cambia las contraseñas predeterminadas en producción');


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
        ]);
    }
}
