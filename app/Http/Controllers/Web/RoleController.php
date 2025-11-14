<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class RoleController extends Controller{
    public function __construct()
    {
        $this->middleware('permission:roles.index|roles.create|roles.edit|roles.destroy', ['only' => ['index', 'store']]);
        $this->middleware('permission:roles.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:roles.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:roles.destroy', ['only' => ['destroy']]);
    }

    public function index(Request $request){
        if ($request->ajax()) {

            $data = Role::select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nombre', function ($role) {
                    $html = '<strong>' . $role->name . '</strong>';
                    if ($role->name === 'Super Admin') {
                        $html .= ' <span class="badge badge-danger ml-2">Protegido</span>';
                    }
                    return $html;
                })
                ->addColumn('permisos', function ($role) {
                    return '<span class="badge badge-info">' .
                           $role->permissions->count() . ' permisos</span>';
                })
                ->addColumn('usuarios', function ($role) {
                    return '<span class="badge badge-secondary">' .
                           $role->users->count() . ' usuarios</span>';
                })
                ->addColumn('fecha_registro', function($role) {
                    return $role->created_at->format('d/m/Y h:i a');
                })
                ->addColumn('action', function($role) {

                    $btn = '<div class="d-flex">';

                    // Botón Ver
                    if (auth()->user()->can('roles.show')) {
                        $btn .= '<a href="' . route('roles.show', $role->id) . '" class="btn bg-gradient-info btn-sm mr-1" title="Ver Detalles">
                                <i class="fas fa-eye"></i>
                            </a>';
                    }

                    // Botón Editar (con verificación de permisos)
                    if (auth()->user()->can('roles.edit')) {
                        $btn .= '<a class="btn bg-gradient-primary btn-sm mr-1" href="' . route('roles.edit', $role->id) . '" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>';
                    }

                    // Botón Eliminar (con verificación de permisos)
                    if (auth()->user()->can('roles.destroy') && !in_array($role->name, ['Super Admin', 'Administrador'])) {
                        $btn .= '<form method="POST" class="formulario-eliminar d-inline" action="' . route('roles.destroy', $role->id) . '">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn bg-gradient-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['nombre', 'permisos', 'usuarios', 'action'])
                ->make(true);
        }

        return view('modulos.rolesusuarios.index');
    }


    /**
     * Mostrar lista de roles
     */
    /* public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('modulos.rolesusuarios.index', compact('roles'));
    } */

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        // Agrupar permisos por módulo
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        })->map(function ($group) {
            return $group->sortBy(function ($permission) {
                $action = explode('.', $permission->name)[1];
                // Ordenar por prioridad de acción
                $order = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
                $pos = array_search($action, $order);
                return $pos !== false ? $pos : 999;
            });
        });

        return view('modulos.rolesusuarios.create', compact('permissions'));
    }

    /**
     * Guardar nuevo rol
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Ya existe un rol con este nombre.',
            'permissions.array' => 'Los permisos deben ser un array.',
            'permissions.*.exists' => 'Uno o más permisos no son válidos.'
        ]);

        try {
            DB::beginTransaction();

            $role = Role::create(['name' => $request->name]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();

            return redirect()->route('roles.index')
                ->with('success', 'Rol creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el rol: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalles del rol
     */
    public function show(Role $role)
    {
        $role->load('permissions');

        // Agrupar permisos por módulo
        $permissionsByModule = $role->permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('modulos.rolesusuarios.show', compact('role', 'permissionsByModule'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Role $role)
    {
        // Agrupar permisos por módulo
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        })->map(function ($group) {
            return $group->sortBy(function ($permission) {
                $action = explode('.', $permission->name)[1];
                $order = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
                $pos = array_search($action, $order);
                return $pos !== false ? $pos : 999;
            });
        });

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('modulos.rolesusuarios.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Actualizar rol
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Ya existe un rol con este nombre.',
            'permissions.array' => 'Los permisos deben ser un array.',
            'permissions.*.exists' => 'Uno o más permisos no son válidos.'
        ]);

        try {
            DB::beginTransaction();

            $role->update(['name' => $request->name]);
            $role->syncPermissions($request->permissions ?? []);

            DB::commit();

            return redirect()->route('roles.index')
                ->with('success', 'Rol actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el rol: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar rol
     */
    public function destroy(Role $role)
    {
        // Prevenir eliminación de roles críticos
        $rolesProtegidos = ['Super Admin', 'Administrador'];

        if (in_array($role->name, $rolesProtegidos)) {
            return redirect()->route('roles.index')
                ->with('error', 'No se puede eliminar el rol ' . $role->name . '.');
        }

        // Verificar si hay usuarios con este rol
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados.');
        }

        try {
            $role->delete();

            return redirect()->route('roles.index')
                ->with('success', 'Rol eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('roles.index')
                ->with('error', 'Error al eliminar el rol: ' . $e->getMessage());
        }
    }
}
