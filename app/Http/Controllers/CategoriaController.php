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

}
