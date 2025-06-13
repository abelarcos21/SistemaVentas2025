<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
use App\Models\Marca;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Exception;

class MarcaController extends Controller
{
    //
    public function index(){

        $marcas = Marca::all();
        return view('modulos.marcas.index', compact('marcas'));

    }

    public function create(){
        return view('modulos.marcas.create');

    }

    public function edit(Marca $marca){

        return view('modulos.marcas.edit', compact('marca'));

    }

    public function store(Request $request){

        // Validación clara y separada
        $validated = $request->validate([
            'nombre'        => 'required|string|max:255',
            'descripcion'      => 'required|string|max:255',
            'activo'         => 'required|boolean',

        ]);

        DB::beginTransaction();

        try {

            Marca::create($validated);

            DB::commit();

            return redirect()->route('marca.index')->with('success', 'Marca creada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar Marca: ' . $e->getMessage());
            return redirect()->route('marca.index')->with('error', 'Error al guardar marca.');
        }
    }

    public function update(Request $request, Marca $marca){

        //validar datos
        $validated = $request->validate([

            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',// gracias al input hidden + checkbox, este campo siempre se enviará

        ]);

        DB::beginTransaction();

        try {

            // Llenar el modelo con los datos validados y guardar
            $marca->fill($validated)->save(); // metodo fill es igual que el método save() pero sin crear un nuevo registro

            DB::commit();

            return redirect()->route('marca.index')->with('success', 'Marca Actualizada Correctamente');


        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al Actualizar Marca:' . $e->getMessage());
            return redirect()->route('marca.index')->with('error', 'Error al Actualizar Marca' . $e->getMessage());
        }

    }

    public function destroy(Marca $marca){

        DB::beginTransaction(); //  Inicia transacción

        try {

            //$marca = Marca::findOrFail($id); //no es nesesario lo inyecto modelbinding
            $nombreMarca = $marca->nombre;
            $marca->delete(); //  Si falla aquí (por clave foránea), lanzará excepción

            DB::commit(); //  Si todo sale bien, confirma transacción

            return redirect()->route('marca.index')->with('success','La Marca  '.$nombreMarca.'  se Elimino');

        } catch (QueryException $e) {

            DB::rollBack(); //  Revierte los cambios

            if($e->getCode() == 23000){
                // Error por clave foránea (productos asociados)
                return redirect()->route('marca.index')->with('error','No se puede eliminar la marca porque está asociada a uno o más productos.');
            }

            return redirect()->route('marca.index')->with('error','Ocurrió un error al eliminar la marca.');

        }catch (Exception $e){
            DB::rollBack(); // Por cualquier otra excepción
            return redirect()->route('marca.index')->with('error','Ocurrió un error inesperado: ' . $e->getMessage());
        }

    }
}
