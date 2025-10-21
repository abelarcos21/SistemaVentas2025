<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class ManagePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:manage {action?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gestionar roles y permisos del sistema';

    /**
     * Execute the console command.
     */
    public function handle(){

        $action = $this->argument('action');

        if (!$action) {
            $action = $this->choice(
                '¿Qué deseas hacer?',
                [
                    'list-roles' => 'Listar todos los roles',
                    'list-permissions' => 'Listar todos los permisos',
                    'assign-role' => 'Asignar rol a usuario',
                    'cache-reset' => 'Limpiar caché de permisos',
                    'show-user' => 'Ver permisos de un usuario',
                ],
                0
            );
        }

        match($action) {
            'list-roles' => $this->listRoles(),
            'list-permissions' => $this->listPermissions(),
            'assign-role' => $this->assignRole(),
            'cache-reset' => $this->cacheReset(),
            'show-user' => $this->showUserPermissions(),
            default => $this->error('Acción no válida')
        };
    }

    /**
     * Listar todos los roles
    */
    private function listRoles(){

        $roles = Role::with('permissions')->get();

        $this->info("\n📋 ROLES DEL SISTEMA\n");

        $tableData = [];
        foreach ($roles as $role) {
            $tableData[] = [
                $role->id,
                $role->name,
                $role->permissions->count(),
                $role->users->count(),
            ];
        }

        $this->table(
            ['ID', 'Nombre', 'Permisos', 'Usuarios'],
            $tableData
        );
    }

    /**
     * Listar todos los permisos
     */
    private function listPermissions(){

        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        $this->info("\n🔑 PERMISOS DEL SISTEMA\n");

        foreach ($permissions as $module => $modulePermissions) {
            $this->warn("📦 Módulo: " . strtoupper($module));

            $tableData = [];
            foreach ($modulePermissions as $permission) {
                $tableData[] = [
                    $permission->id,
                    $permission->name,
                ];
            }

            $this->table(['ID', 'Permiso'], $tableData);
            $this->newLine();
        }
    }

    /**
     * Asignar rol a usuario
    */
    private function assignRole(){

        $users = User::all();
        $roles = Role::all();

        if ($users->isEmpty()) {
            $this->error('No hay usuarios en el sistema');
            return;
        }

        // Seleccionar usuario
        $userChoices = $users->mapWithKeys(function ($user) {
            return [$user->id => "{$user->name} ({$user->email})"];
        })->toArray();

        $userId = $this->choice('Selecciona un usuario:', $userChoices);
        $user = User::find($userId);

        // Seleccionar rol
        $roleChoices = $roles->mapWithKeys(function ($role) {
            return [$role->id => $role->name];
        })->toArray();

        $roleId = $this->choice('Selecciona un rol:', $roleChoices);
        $role = Role::find($roleId);

        // Asignar rol
        $user->syncRoles([$role]);

        $this->info("✅ Rol '{$role->name}' asignado a {$user->name}");
    }

    /**
     * Limpiar caché de permisos
    */
    private function cacheReset(){

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->info('✅ Caché de permisos limpiado correctamente');
    }

    /**
     * Ver permisos de un usuario
    */
    private function showUserPermissions(){

        $users = User::with('roles.permissions')->get();

        if ($users->isEmpty()) {
            $this->error('No hay usuarios en el sistema');
            return;
        }

        $userChoices = $users->mapWithKeys(function ($user) {
            return [$user->id => "{$user->name} ({$user->email})"];
        })->toArray();

        $userId = $this->choice('Selecciona un usuario:', $userChoices);
        $user = User::with('roles.permissions')->find($userId);

        $this->info("\n👤 Usuario: {$user->name}");
        $this->info("📧 Email: {$user->email}\n");

        if ($user->roles->isEmpty()) {
            $this->warn('Este usuario no tiene roles asignados');
            return;
        }

        foreach ($user->roles as $role) {
            $this->warn("🎭 Rol: {$role->name}");

            if ($role->permissions->isEmpty()) {
                $this->line("   Sin permisos");
                continue;
            }

            $permissionsByModule = $role->permissions->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            });

            foreach ($permissionsByModule as $module => $permissions) {
                $this->line("   📦 {$module}:");
                foreach ($permissions as $permission) {
                    $this->line("      ✓ {$permission->name}");
                }
            }
            $this->newLine();
        }
    }

}
