<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsuarioController extends Controller
{
    //index
    public function index(){
        $usuarios = User::all();
        return view('modulos.usuarios.index', compact('usuarios'));
    }

    public function create(){
        return view('modulos.categorias.create');

    }
    public function store(Request $request){

        $validated = $request->validate([

            'nombre' => 'required|string|max:255',

        ]);

        $validated['user_id'] = Auth::user()->id;

        Categoria::create($validated);


        return redirect()->route('categoria.index')->with('success', 'Categoria Creada Correctamente');

    }

    public function show(User $user){
        return view('modulos.categorias.show', compact('user'));
    }

    public function edit(User $user){
        return view('modulos.categorias.edit', compact('user'));

    }


    public function update(Request $request, User $user){

        $validated = $request->validate([

            'nombre' => 'required|string|max:255',

        ]);



        $categoria->fill($validated); // metodo fill es igual que el método save() pero sin crear un nuevo registro

        $categoria->save();

        return redirect()->route('categoria.index')->with('success', 'Categoria Actualizada Correctamente');

    }

    public function destroy(User $user){
        $nombreCategoria = $categoria->nombre;
        $categoria->delete();
        return redirect()->route('categoria.index')->with('success','La Categoria' .$nombreCategoria.'se Elimino Correctamente');

    }


}
