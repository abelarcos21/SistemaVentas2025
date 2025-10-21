<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder{

    /**
     * Run the database seeds.
    */
    public function run(): void{

        // Verificar si ya existe el usuario Super Admin
        $superAdmin = User::where('email', 'admin@admin.com')->first();

        if (!$superAdmin) {
            // Crear usuario Super Admin
            $superAdmin = User::create([
                'name' => 'Super Administrador',
                'email' => 'admin@admin.com',
                'password' => Hash::make('12345678'), // Cambia esto en producción
                'activo' => true,
            ]);

            $this->command->info('✅ Usuario Super Admin creado:');
            $this->command->info('   Email: admin@admin.com');
            $this->command->info('   Password: 12345678');
        } else {
            $this->command->info('ℹ️  El usuario Super Admin ya existe');
        }

        // Asignar rol Super Admin
        $roleSuperAdmin = Role::where('name', 'Super Admin')->first();

        if ($roleSuperAdmin) {
            $superAdmin->syncRoles([$roleSuperAdmin]);
            $this->command->info('✅ Rol "Super Admin" asignado al usuario');
        }

        // Crear usuarios de ejemplo para otros roles
        $this->createExampleUsers();
    }

    /**
     * Crear usuarios de ejemplo
    */
    private function createExampleUsers(){
        $users = [
            [
                'name' => 'Gerente General',
                'email' => 'gerente@empresa.com',
                'password' => '12345678',
                'role' => 'Gerente'
            ],
            [
                'name' => 'Supervisor de Ventas',
                'email' => 'supervisor@empresa.com',
                'password' => '12345678',
                'role' => 'Supervisor'
            ],
            [
                'name' => 'Vendedor Principal',
                'email' => 'vendedor@empresa.com',
                'password' => '12345678',
                'role' => 'Vendedor'
            ],
            [
                'name' => 'Cajero Principal',
                'email' => 'cajero@empresa.com',
                'password' => '12345678',
                'role' => 'Cajero'
            ],
            [
                'name' => 'Encargado de Almacén',
                'email' => 'almacen@empresa.com',
                'password' => '12345678',
                'role' => 'Almacenero'
            ],
        ];

        foreach ($users as $userData) {
            // Verificar si el usuario ya existe
            $existingUser = User::where('email', $userData['email'])->first();

            if (!$existingUser) {
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'activo' => true,
                ]);

                // Asignar rol
                $role = Role::where('name', $userData['role'])->first();
                if ($role) {
                    $user->assignRole($role);
                    $this->command->info("✅ Usuario {$userData['name']} creado con rol {$userData['role']}");
                }
            } else {
                $this->command->info("ℹ️  Usuario {$userData['email']} ya existe");
            }
        }

        $this->command->info("\n📧 Credenciales de acceso:");
        $this->command->info("   Password para todos: 12345678");
        $this->command->warn("\n⚠️  Recuerda cambiar las contraseñas en producción!");
    }
}
