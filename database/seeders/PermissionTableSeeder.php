<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Crear permisos por módulos
        $permissions = [

            //Productos
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',

            //Roles
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            // Ventas
            'ventas.index',
            'ventas.create',
            'ventas.store',
            'ventas.show',
            'ventas.edit',
            'ventas.update',
            'ventas.destroy',
            
            // Inventario
            'inventario.index',
            'inventario.create',
            'inventario.store',
            'inventario.show',
            'inventario.edit',
            'inventario.update',
            
            // Reportes
            'reportes.index',
            'reportes.ventas',
            'reportes.inventario',
            
            // Configuración
            'configuracion.index',
            'configuracion.usuarios',
            'configuracion.sistema',
            
            // Clientes
            'clientes.index',
            'clientes.create',
            'clientes.store',
            'clientes.edit',
            'clientes.update',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $cajero = Role::firstOrCreate(['name' => 'cajero']);
        $supervisor = Role::firstOrCreate(['name' => 'supervisor']);
        $administrador = Role::firstOrCreate(['name' => 'administrador']);

        // Asignar permisos al cajero (solo ventas y clientes básicos)
        $cajero->syncPermissions([
            'ventas.index',
            'ventas.create',
            'ventas.store',
            'ventas.show',
            'clientes.index',
            'clientes.create',
            'clientes.store',
        ]);

        // Supervisor tiene más permisos
        $supervisor->syncPermissions([
            'ventas.index',
            'ventas.create',
            'ventas.store',
            'ventas.show',
            'ventas.edit',
            'ventas.update',
            'inventario.index',
            'inventario.show',
            'reportes.index',
            'reportes.ventas',
            'clientes.index',
            'clientes.create',
            'clientes.store',
            'clientes.edit',
            'clientes.update',
        ]);

        // Administrador tiene todos los permisos
        $administrador->syncPermissions(Permission::all());
    }
}
