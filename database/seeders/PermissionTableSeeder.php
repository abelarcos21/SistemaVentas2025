<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionTableSeeder extends Seeder{

    public function run(): void
    {
        // Resetear caché de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Definir permisos por módulos de forma consistente
        $modules = [
            'productos' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'toggle-activo', 'imprimir-etiquetas'],
            'categorias' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'toggle-activo'],
            'marcas' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'toggle-activo'],
            'proveedores' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'toggle-activo'],
            'clientes' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'toggle-activo', 'stats'],
            'ventas' => ['index', 'create', 'store', 'show', 'destroy'],
            'pos' => ['index', 'buscar', 'agregar-carrito', 'quitar-carrito', 'actualizar-carrito', 'borrar-carrito'],
            'cotizaciones' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'convertir', 'cancelar', 'pdf'],
            'compras' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'],
            'cajas' => ['index', 'abrir', 'cerrar', 'movimiento'],
            'reportes' => ['index', 'productos', 'ventas', 'inventario', 'falta-stock'],
            'usuarios' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'toggle-activo', 'cambiar-password'],
            'roles' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'],
            'negocio' => ['edit', 'update', 'perfil'],
        ];

        // Crear permisos
        $allPermissions = [];
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissionName = "$module.$action";
                Permission::firstOrCreate(['name' => $permissionName]);
                $allPermissions[] = $permissionName;
            }
        }

        // ============================================
        // CREAR ROLES Y ASIGNAR PERMISOS
        // ============================================

        // 1. ROL: CAJERO (Permisos básicos de ventas)
        $cajero = Role::firstOrCreate(['name' => 'Cajero']);
        $cajero->syncPermissions([
            // POS - Punto de Venta
            'pos.index',
            'pos.buscar',
            'pos.agregar-carrito',
            'pos.quitar-carrito',
            'pos.actualizar-carrito',
            'pos.borrar-carrito',

            // Ventas (crear y ver)
            'ventas.index',
            'ventas.create',
            'ventas.store',
            'ventas.show',

            // Clientes (consultar y crear básico)
            'clientes.index',
            'clientes.create',
            'clientes.store',
            'clientes.show',

            // Productos (solo consulta)
            'productos.index',
            'productos.show',

            // Cajas
            'cajas.index',
            'cajas.abrir',
            'cajas.cerrar',
            'cajas.movimiento',

            // Cotizaciones (consultar y crear)
            'cotizaciones.index',
            'cotizaciones.create',
            'cotizaciones.store',
            'cotizaciones.show',
            'cotizaciones.pdf',
        ]);

        // 2. ROL: VENDEDOR (Cajero + gestión completa de ventas y cotizaciones)
        $vendedor = Role::firstOrCreate(['name' => 'Vendedor']);
        $vendedor->syncPermissions([
            // Todo lo del Cajero
            ...$cajero->permissions->pluck('name')->toArray(),

            // Ventas completas
            'ventas.destroy',

            // Clientes completo
            'clientes.edit',
            'clientes.update',
            'clientes.stats',

            // Cotizaciones completo
            'cotizaciones.edit',
            'cotizaciones.update',
            'cotizaciones.destroy',
            'cotizaciones.convertir',
            'cotizaciones.cancelar',

            // Reportes básicos
            'reportes.index',
            'reportes.ventas',
        ]);

        // 3. ROL: ALMACENERO (Gestión de inventario y compras)
        $almacenero = Role::firstOrCreate(['name' => 'Almacenero']);
        $almacenero->syncPermissions([
            // Productos completo
            'productos.index',
            'productos.create',
            'productos.store',
            'productos.show',
            'productos.edit',
            'productos.update',
            'productos.destroy',
            'productos.toggle-activo',
            'productos.imprimir-etiquetas',

            // Categorías completo
            'categorias.index',
            'categorias.create',
            'categorias.store',
            'categorias.show',
            'categorias.edit',
            'categorias.update',
            'categorias.destroy',
            'categorias.toggle-activo',

            // Marcas completo
            'marcas.index',
            'marcas.create',
            'marcas.store',
            'marcas.show',
            'marcas.edit',
            'marcas.update',
            'marcas.destroy',
            'marcas.toggle-activo',

            // Proveedores completo
            'proveedores.index',
            'proveedores.create',
            'proveedores.store',
            'proveedores.show',
            'proveedores.edit',
            'proveedores.update',
            'proveedores.destroy',
            'proveedores.toggle-activo',

            // Compras completo
            'compras.index',
            'compras.create',
            'compras.store',
            'compras.show',
            'compras.edit',
            'compras.update',
            'compras.destroy',

            // Reportes de inventario
            'reportes.index',
            'reportes.productos',
            'reportes.inventario',
            'reportes.falta-stock',
        ]);

        // 4. ROL: SUPERVISOR (Vendedor + Reportes completos + algunas configuraciones)
        $supervisor = Role::firstOrCreate(['name' => 'Supervisor']);
        $supervisor->syncPermissions([
            // Todo lo del Vendedor
            ...$vendedor->permissions->pluck('name')->toArray(),

            // Productos (consulta y edición básica)
            'productos.index',
            'productos.show',
            'productos.edit',
            'productos.update',
            'productos.toggle-activo',

            // Clientes completo
            'clientes.destroy',
            'clientes.toggle-activo',

            // Reportes completos
            'reportes.productos',
            'reportes.inventario',
            'reportes.falta-stock',

            // Cajas (completo)
            'cajas.index',
            'cajas.abrir',
            'cajas.cerrar',
            'cajas.movimiento',
        ]);

        // 5. ROL: GERENTE (Supervisor + Almacenero + gestión de usuarios básica)
        $gerente = Role::firstOrCreate(['name' => 'Gerente']);
        $gerente->syncPermissions([
            // Todo lo del Supervisor
            ...$supervisor->permissions->pluck('name')->toArray(),

            // Todo lo del Almacenero
            ...$almacenero->permissions->pluck('name')->toArray(),

            // Usuarios (solo consulta y edición)
            'usuarios.index',
            'usuarios.show',
            'usuarios.edit',
            'usuarios.update',
            'usuarios.toggle-activo',

            // Negocio (consulta)
            'negocio.perfil',
        ]);

        // 6. ROL: ADMINISTRADOR (Todos los permisos)
        $administrador = Role::firstOrCreate(['name' => 'Administrador']);
        $administrador->syncPermissions(Permission::all());

        // 7. ROL: SUPER ADMIN (Todos los permisos - no puede ser eliminado)
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        $this->command->info('✅ Roles y permisos creados exitosamente!');
    }
}
