<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
use App\Models\Categoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Exception;

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

        //validar datos
        $validated = $request->validate([

            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'medida' => 'required|string|max:100',
            'activo' => 'required|boolean',// gracias al input hidden + checkbox, este campo siempre se enviará

        ]);

        DB::beginTransaction();

        try{

            $validated['user_id'] = Auth::user()->id;

            Categoria::create($validated);

            DB::commit();

            return redirect()->route('categoria.index')->with('success', 'Categoria Creada Correctamente');

        }catch(Exception $e){

            DB::rollBack();
            Log::error('Error al Guardar Categoria:' . $e->getMessage());
            return redirect()->route('categoria.index')->with('error', 'Error al Guardar Categoria' . $e->getMessage());
        }

    }

    public function show(Categoria $categoria){
        return view('modulos.categorias.show', compact('categoria'));
    }

    public function edit(Categoria $categoria){
        return view('modulos.categorias.edit', compact('categoria'));

    }


    public function update(Request $request, Categoria $categoria){

        //validar datos
        $validated = $request->validate([

            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'medida' => 'required|string|max:100',
            'activo' => 'required|boolean',// gracias al input hidden + checkbox, este campo siempre se enviará

        ]);

        DB::beginTransaction();

        try {

            // Llenar el modelo con los datos validados y guardar
            $categoria->fill($validated)->save(); // metodo fill es igual que el método save() pero sin crear un nuevo registro

            DB::commit();

            return redirect()->route('categoria.index')->with('success', 'Categoria Actualizada Correctamente');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al Actualizar Categoria:' . $e->getMessage());
            return redirect()->route('categoria.index')->with('error', 'Error al Actualizar Categoria' . $e->getMessage());

        }

    }

    public function destroy(Categoria $categoria){

        DB::beginTransaction(); //  Inicia transacción

        try {

            //$categoria = Categoria::findOrFail($id); //no es nesesario lo inyecto modelbinding
            $nombreCategoria = $categoria->nombre;
            $categoria->delete(); //  Si falla aquí (por clave foránea), lanzará excepción

            DB::commit(); //  Si todo sale bien, confirma transacción

            return redirect()->route('categoria.index')->with('success','La Categoria  '.$nombreCategoria.'  se Elimino');

        } catch (QueryException $e) {

            DB::rollBack(); //  Revierte los cambios

            if($e->getCode() == 23000){
                // Error por clave foránea (productos asociados)
                return redirect()->route('categoria.index')->with('error','No se puede eliminar la categoria porque está asociada a uno o más productos.');
            }

            return redirect()->route('categoria.index')->with('error','Ocurrió un error al eliminar la categoria.');

        }catch (Exception $e){
            DB::rollBack(); // Por cualquier otra excepción
            return redirect()->route('categoria.index')->with('error','Ocurrió un error inesperado: ' . $e->getMessage());
        }

    }

}
