<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        // Admin fijo
        User::create([
            'name' => 'Admin Principal',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'), // contraseña segura
            'activo' => true,
            'rol' => 'admin',
        ]);

        // 5 Cajeros generados aleatoriamente
        User::factory()->count(5)->create([
            'rol' => 'cajero',
            'activo' => true,
        ]);

    }
}
