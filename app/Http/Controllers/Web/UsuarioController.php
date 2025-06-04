<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Hash;
use Exception;
class UsuarioController extends Controller
{
    //index
    public function index(){
        $usuarios = User::all();
        return view('modulos.usuarios.index', compact('usuarios'));
    }

    public function create(){
        return view('modulos.usuarios.create');

    }
    public function store(Request $request){


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



    }

    //CAMBIAR ESTADO DE ACTIVO
    public function cambiarEstado(Request $request, $id){
        $usuario = User::findOrFail($id);
        $usuario->activo = $request->activo;
        $usuario->save();
        return response()->json(['message' => 'Estado Actualizado Correctamente']);
    }

    //CAMBIAR, ACTUALIZAR CONTRASEÑA
    public function cambiarPassword(Request $request){
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => 'required|string|min:6',
        ]);

        $usuario = User::findOrFail($request->user_id);
        $usuario->password = bcrypt($request->password);
        $usuario->save();
        return response()->json(['success' => true]);
    }

    public function show(User $user){
        return view('modulos.usuarios.show', compact('user'));
    }

    public function edit(User $user){
        return view('modulos.usuarios.edit', compact('user'));

    }


    public function update(Request $request, User $user){

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

    }

    public function destroy(User $user){
        $nombreCategoria = $categoria->nombre;
        $categoria->delete();
        return redirect()->route('categoria.index')->with('success','La Categoria' .$nombreCategoria.'se Elimino Correctamente');

    }


}
