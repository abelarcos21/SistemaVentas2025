<?php

namespace App\Helpers;

class PermissionHelper
{
    /**
     * Traduce el nombre del módulo al español
     */
    public static function translateModule($module)
    {
        $translations = [
            'productos' => 'Productos',
            'categorias' => 'Categorías',
            'marcas' => 'Marcas',
            'proveedores' => 'Proveedores',
            'clientes' => 'Clientes',
            'ventas' => 'Ventas',
            'pos' => 'Punto de Venta',
            'cotizaciones' => 'Cotizaciones',
            'compras' => 'Compras',
            'cajas' => 'Cajas',
            'reportes' => 'Reportes',
            'usuarios' => 'Usuarios',
            'roles' => 'Roles',
            'negocio' => 'Negocio',
        ];

        return $translations[$module] ?? ucfirst($module);
    }

    /**
     * Traduce la acción al español
     */
    public static function translateAction($action)
    {
        $translations = [
            'index' => 'Ver listado',
            'create' => 'Crear',
            'store' => 'Guardar',
            'show' => 'Ver detalle',
            'edit' => 'Editar',
            'update' => 'Actualizar',
            'destroy' => 'Eliminar',
            'toggle-activo' => 'Cambiar estado',
            'imprimir-etiquetas' => 'Imprimir etiquetas',
            'stats' => 'Ver estadísticas',
            'buscar' => 'Buscar',
            'agregar-carrito' => 'Agregar al carrito',
            'quitar-carrito' => 'Quitar del carrito',
            'actualizar-carrito' => 'Actualizar carrito',
            'borrar-carrito' => 'Borrar carrito',
            'convertir' => 'Convertir a venta',
            'cancelar' => 'Cancelar',
            'pdf' => 'Generar PDF',
            'abrir' => 'Abrir',
            'cerrar' => 'Cerrar',
            'movimiento' => 'Registrar movimiento',
            'productos' => 'Reporte de productos',
            'ventas' => 'Reporte de ventas',
            'inventario' => 'Reporte de inventario',
            'falta-stock' => 'Productos sin stock',
            'cambiar-password' => 'Cambiar contraseña',
            'perfil' => 'Ver perfil',
        ];

        return $translations[$action] ?? ucfirst($action);
    }

    /**
     * Obtiene el icono para el módulo
     */
    public static function getModuleIcon($module)
    {
        $icons = [
            'productos' => 'fa-box',
            'categorias' => 'fa-tags',
            'marcas' => 'fa-copyright',
            'proveedores' => 'fa-truck',
            'clientes' => 'fa-users',
            'ventas' => 'fa-shopping-cart',
            'pos' => 'fa-cash-register',
            'cotizaciones' => 'fa-file-invoice',
            'compras' => 'fa-shopping-basket',
            'cajas' => 'fa-money-bill-wave',
            'reportes' => 'fa-chart-bar',
            'usuarios' => 'fa-user',
            'roles' => 'fa-user-shield',
            'negocio' => 'fa-store',
        ];

        return $icons[$module] ?? 'fa-circle';
    }

    /**
     * Obtiene el color badge para cada acción
     */
    public static function getActionBadgeClass($action)
    {
        $classes = [
            'index' => 'badge-info',
            'create' => 'badge-success',
            'store' => 'badge-success',
            'show' => 'badge-primary',
            'edit' => 'badge-warning',
            'update' => 'badge-warning',
            'destroy' => 'badge-danger',
            'toggle-activo' => 'badge-secondary',
        ];

        return $classes[$action] ?? 'badge-secondary';
    }
}
