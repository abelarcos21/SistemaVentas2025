<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
use App\Models\Marca;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
}
