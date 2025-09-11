<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Exception;
use Yajra\DataTables\DataTables;

class UsuarioController extends Controller
{
    //index
   /*  public function index(Request $request): View {

       /*  $usuarios = User::latest()->paginate(5);

        return view('modulos.usuarios.index',compact('usuarios'))->with('i', ($request->input('page', 1) - 1) * 5); */
       /*  $usuarios = User::all();
        return view('modulos.usuarios.index', compact('usuarios')); */
    /* }  */

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
                            <a href="' . route('usuario.show', $row) . '" class="btn btn-info bg-gradient-info btn-sm mr-1">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <a href="' . route('usuario.edit', $row) . '" class="btn btn-info bg-gradient-info btn-sm mr-1">
                                <i class="fas fa-user"></i> <i class="fas fa-pen"></i> Editar
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

    public function create(): View {

        $roles = Role::pluck('name','name')->all();

        return view('modulos.usuarios.create',compact('roles'));
       /*  return view('modulos.usuarios.create'); */

    }

    public function store(Request $request): RedirectResponse{
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'activo' => 'required|boolean', //gracias al input hidden + checkbox, este campo siempre se enviará
            'roles' => 'required'
        ]);

        $validated = $request->all();
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user->assignRole($request->input('roles'));

        return redirect()->route('usuario.index')
                        ->with('success','Usuario guardado con exito!');
    }
    /* public function store(Request $request){

        try {

            $validated = $request->validate([

                'name' => 'required|string|max:255',
                'email' => 'required',
                'password' => 'required',
                'activo' => 'nullable|boolean',
                'rol' => 'required',

            ]);

            $validated['activo'] = $request->has('activo'); // El switch solo envía el valor si está activado

            User::create($validated);

            session()->flash('swal', [

                'icon' => 'success',
                'title' => 'Usuario creado correctamente',
                'text' => 'Bien Hecho!',
                'draggable' => 'true',

            ]);

            //return to_route('usuario.index')->with('success', 'Usuario guardado con exito!');
            return redirect()->route('usuario.index');


        } catch (Exception $e) {
            return to_route('usuario.create')->with('error', 'Error al guardar usuario!' . $e->getMessage());
        }
    } */

    //CAMBIAR ESTADO DE ACTIVO VIA AJAX
    public function toggleActivo(Request $request){
        try {
            $cliente = User::findOrFail($request->id);
            $cliente->activo = $request->activo;
            $cliente->save();

            return response()->json([
                'success' => true,
                'message' => $request->activo ? 'Usuario activado correctamente' : 'Usuario desactivado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado'
            ], 500);
        }
    }

    //CAMBIAR, ACTUALIZAR CONTRASEÑA
    public function cambiarPassword(Request $request){
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => 'required|string|min:8',

        ]);

        $usuario = User::findOrFail($request->user_id);
        $usuario->password = bcrypt($request->password);
        $usuario->save();
        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente'
        ]);
    }

    public function show($id): View {
        $user = User::find($id);

        return view('modulos.usuarios.show',compact('user'));
    }

   /*  public function show(User $user){
        return view('modulos.usuarios.show', compact('user'));
    } */

     public function edit($id): View {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();

        return view('modulos.usuarios.edit',compact('user','roles','userRole'));
    }

    /* public function edit(User $user){
        return view('modulos.usuarios.edit', compact('user'));

    } */


    public function update(Request $request, $id): RedirectResponse {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'activo' => 'required|boolean', //gracias al input hidden + checkbox, este campo siempre se enviará
            'roles' => 'required'
        ]);

        $input = $request->all();
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));
        }

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $user->assignRole($request->input('roles'));

        return redirect()->route('usuario.index')
                        ->with('success','Usuario actualizado correctamente');
    }

   /*  public function update(Request $request, User $user){

        // Validar los datos
        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'activo' => ['nullable', 'boolean'],
            'rol'    => ['required', 'string'],
        ]);

        DB::beginTransaction();

        try {

            $user->fill($validated)->save();// Llenar el modelo con los datos validados y guardar

            DB::commit();

            return redirect()->route('usuario.index')->with('success', 'Usuario actualizado correctamente');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar usuario: ' . $e->getMessage());

            return redirect()->route('usuario.index')->with('error', 'Error al actualizar usuario: ' . $e->getMessage());
        }

    } */

    public function destroy($id): RedirectResponse {
        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }

   /*  public function destroy(User $user){
        $nombreCategoria = $categoria->nombre;
        $categoria->delete();
        return redirect()->route('categoria.index')->with('success','La Categoria' .$nombreCategoria.'se Elimino Correctamente');

    } */


}
