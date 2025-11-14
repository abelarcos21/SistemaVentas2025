<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; //IMPORTANTE: esta línea importa la clase base
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Arr;

class UsuarioController extends Controller
{

    public function __construct(){
        $this->middleware('permission:usuarios.index|usuarios.create|usuarios.edit|usuarios.destroy', ['only' => ['index', 'show']]);
        $this->middleware('permission:usuarios.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:usuarios.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:usuarios.destroy', ['only' => ['destroy']]);
    }

    /**
     *Metodo Index.
    */
    public function index(Request $request){
        if ($request->ajax()) {

            $data = User::with('roles')->select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('roles', function($row) {
                    $roles = '';
                    if($row->roles->count() > 0) {
                        foreach($row->roles as $role) {
                            $roles .= '<span class="badge bg-success mr-1">' . $role->name . '</span>';
                        }
                    }
                    return $roles;
                })
                ->addColumn('fecha_registro', function($row) {
                    return $row->created_at->format('d/m/Y');
                })
                ->addColumn('activo', function($row) {
                    $checked = $row->activo ? 'checked' : '';
                    return '<div class="custom-control custom-switch toggle-estado">
                                <input role="switch" type="checkbox" class="custom-control-input toggle-activo"
                                       id="activoSwitch'.$row->id.'" '.$checked.' data-id="'.$row->id.'">
                                <label class="custom-control-label" for="activoSwitch'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('cambio_password', function($row) {
                    return '
                        <a class="btn btn-info bg-gradient-primary btnCambioPassword" data-id="' . $row->id . '">
                            <i class="fas fa-user"></i> <i class="fas fa-lock"></i>
                        </a>
                    ';
                })
                ->addColumn('action', function($row) {
                    $btn = '
                        <div class="d-inline-flex justify-content-center">
                            <a href="' . route('usuario.show', $row) . '" class="btn bg-gradient-info btn-sm mr-1" title="Ver Detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="' . route('usuario.edit', $row) . '" class="btn bg-gradient-primary btn-sm mr-1" title="Editar">
                                <i class="fas fa-user"></i> <i class="fas fa-pen"></i>
                            </a>
                        </div>
                    ';
                    return $btn;
                })
                ->rawColumns(['roles', 'activo', 'cambio_password', 'action'])
                ->make(true);
        }

        return view('modulos.usuarios.index');
    }

    /**
     * Mostrar formulario de creación
    */
    public function create(){

        $roles = Role::pluck('name','name')->all();

        return view('modulos.usuarios.create',compact('roles'));

    }

    /**
    * Guardar nuevo usuario
    */
    public function store(Request $request){

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'activo' => 'required|boolean', //gracias al input hidden + checkbox, este campo siempre se enviará
            'roles' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $validated = $request->all();
            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);
            $user->syncRoles($request->roles);

            DB::commit();

            return redirect()->route('usuario.index')
                ->with('success', 'Usuario creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado activo del usuario (AJAX)
    */
    public function toggleActivo(Request $request){
        $user = User::findOrFail($request->id);

        // Prevenir desactivar al propio usuario
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes desactivar tu propia cuenta.'
            ], 403);
        }

        // Prevenir desactivar Super Admin
        if ($user->hasRole('Super Admin')) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede desactivar un usuario Super Admin.'
            ], 403);
        }

        $user->activo = !$user->activo;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => $user->activo ? 'Usuario activado correctamente' : 'Usuario desactivado correctamente.'
        ]);
    }


    /**
     * Cambiar contraseña del usuario
    */
    public function cambiarPassword(Request $request){
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => 'required|string|min:8',
            /* 'password' => 'required|string|min:8|confirmed', */
        ]); /* , [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]); */

        try {
            $usuario = User::findOrFail($request->user_id);

            $usuario->password = Hash::make($request->password);

            $usuario->save();

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar la contraseña: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Mostrar detalles del usuario
    */
    public function show(User $user){
        $user->load('roles.permissions');
        return view('modulos.usuarios.show', compact('user'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(User $user)
    {
        $roles = Role::pluck('name','name')->all();
        $userRoles = $user->roles->pluck('name','name')->all();

        return view('modulos.usuarios.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Actualizar usuario
    */
    public function update(Request $request, User $user){

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'same:confirm-password',
            'activo' => 'required|boolean', //gracias al input hidden + checkbox, este campo siempre se enviará
            'roles' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $userData = $request->all();

            if(!empty($userData['password'])){
                $userData['password'] = Hash::make($userData['password']);
            }else{
                $userData = Arr::except($userData,array('password'));
            }


            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);
            $user->syncRoles($request->roles);

            DB::commit();

            return redirect()->route('usuario.index')
                ->with('success', 'Usuario actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }


    /**
     * Eliminar usuario
    */
    public function destroy(User $user){

        // Prevenir que el usuario se elimine a sí mismo
        if ($user->id === auth()->id()) {
            return redirect()->route('usuario.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // Prevenir eliminar usuarios con rol Super Admin
        if ($user->hasRole('Super Admin')) {
            return redirect()->route('usuario.index')
                ->with('error', 'No se puede eliminar un usuario con rol Super Admin.');
        }

        try {
            $user->delete();

            return redirect()->route('usuario.index')
                ->with('success', 'Usuario eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('usuario.index')
                ->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }


}
