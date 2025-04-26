<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;

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

    public function show(User $user){
        return view('modulos.usuarios.show', compact('user'));
    }

    public function edit(User $user){
        return view('modulos.usuarios.edit', compact('user'));

    }


    public function update(Request $request, User $user){


        $validated = $request->validate([

            'name' => 'required|string|max:255',
            'email' => 'required',
            'activo' => 'nullable|boolean',
            'rol' => 'required',

        ]);

        $validated['activo'] = $request->has('activo');// El switch solo envía el valor si está activado

        $user->fill($validated); // metodo fill es igual que el método save() pero sin crear un nuevo registro

        $user->save();

        session()->flash('swal', [

            'icon' => 'success',
            'title' => 'Usuario Actualizado correctamente',
            'text' => 'Bien Hecho!',
            'draggable' => 'true',

        ]);

        return redirect()->route('usuario.index');

    }

    public function destroy(User $user){
        $nombreCategoria = $categoria->nombre;
        $categoria->delete();
        return redirect()->route('categoria.index')->with('success','La Categoria' .$nombreCategoria.'se Elimino Correctamente');

    }


}
