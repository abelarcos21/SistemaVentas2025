<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    //constructor
    function __construct(){
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
        $this->middleware('permission:role-create', ['only' => ['create','store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }


    /* public function index(Request $request): View {
        $roles = Role::orderBy('id','DESC')->paginate(5);
        return view('modulos.rolesusuarios.index',compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    } */

    public function index(Request $request){
        if ($request->ajax()) {
            $data = Role::select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name_badge', function($row) {
                    return '<span class="badge bg-success">' . $row->name . '</span>';
                })
                ->addColumn('fecha_registro', function($row) {
                    return $row->created_at->format('d/m/Y h:i a');
                })
                ->addColumn('action', function($row) {
                    $btn = '<div class="d-flex">';

                    // Botón Ver
                    $btn .= '<a href="' . route('roles.show', $row->id) . '" class="btn bg-gradient-info btn-sm mr-1">
                                <i class="fas fa-eye"></i> Ver
                            </a>';

                    // Botón Editar (con verificación de permisos)
                    if (auth()->user()->can('role-edit')) {
                        $btn .= '<a class="btn bg-gradient-warning btn-sm mr-1" href="' . route('roles.edit', $row->id) . '">
                                    <i class="fas fa-edit"></i> Editar
                                </a>';
                    }

                    // Botón Eliminar (con verificación de permisos)
                    if (auth()->user()->can('role-delete')) {
                        $btn .= '<form method="POST" class="formulario-eliminar d-inline" action="' . route('roles.destroy', $row->id) . '">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn bg-gradient-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </button>
                                </form>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['name_badge', 'action'])
                ->make(true);
        }

        return view('modulos.rolesusuarios.index');
    }

    public function create(): View {
        $permission = Permission::get();
        return view('modulos.rolesusuarios.create',compact('permission'));
    }

    public function store(Request $request): RedirectResponse {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $permissionsID = array_map(
            function($value) { return (int)$value; },
            $request->input('permission')
        );

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($permissionsID);

        return redirect()->route('roles.index')
                        ->with('success','Rol Creado Correctamente');
    }

    public function show($id): View {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();

        return view('modulos.rolesusuarios.show',compact('role','rolePermissions'));
    }

    public function edit($id): View {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();

        return view('modulos.rolesusuarios.edit',compact('role','permission','rolePermissions'));
    }

    public function update(Request $request, $id): RedirectResponse {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $permissionsID = array_map(
            function($value) { return (int)$value; },
            $request->input('permission')
        );

        $role->syncPermissions($permissionsID);

        return redirect()->route('roles.index')
                        ->with('success','Rol Actualizado Correctamente');
    }

    public function destroy($id): RedirectResponse {
        DB::table("roles")->where('id',$id)->delete();
        return redirect()->route('roles.index')
                        ->with('success','Rol Eliminado Correctamente');
    }
}
