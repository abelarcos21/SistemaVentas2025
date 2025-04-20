<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use Auth;

class CategoriaController extends Controller
{
    //index

    public function index(){

        $categorias = Categoria::all();

        return view('modulos.categorias.index', compact('categorias'));

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

    public function show(Categoria $categoria){
        return view('modulos.categorias.show', compact('categoria'));
    }

    public function edit(Categoria $categoria){
        return view('modulos.categorias.edit', compact('categoria'));

    }


    public function update(Request $request, Categoria $categoria){

        $validated = $request->validate([

            'nombre' => 'required|string|max:255',

        ]);



        $categoria->fill($validated); // metodo fill es igual que el método save() pero sin crear un nuevo registro

        $categoria->save();

        return redirect()->route('categoria.index')->with('success', 'Categoria Actualizada Correctamente');

    }

    public function destroy(Categoria $categoria){
        $nombreCategoria = $categoria->nombre;
        $categoria->delete();
        return redirect()->route('categoria.index')->with('success','La Categoria' .$nombreCategoria.'se Elimino Correctamente');

    }

}
