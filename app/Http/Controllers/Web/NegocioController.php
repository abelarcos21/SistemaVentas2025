<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class NegocioController extends Controller
{
    //
    public function create(){
        return view('modulos.negocio.informacion');
    }

    public function perfil(){
        return view('modulos.configuracion.perfil');
    }

    public function store(Request $request){

        // Validación clara y separada
        $validated = $request->validate([
            'razon_social' => 'required|string|max:255',
            'rfc' => 'required|string|max:13|unique:empresas',
            'telefono' => 'required|string',
            'correo' => 'required|email|unique:empresas',
            'moneda' => 'required|string',
            'imagen' => 'nullable|image|max:2048',
            'direccion' => 'nullable|string|max:500',

        ]);

        DB::beginTransaction();

        try {

            Empresa::create($validated);

            DB::commit();

            return redirect()->route('negocio.create')->with('success', 'Informacion Negocio Creada Correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error al guardar Informacion Negocio: ' . $e->getMessage());

            return redirect()->route('negocio.create')->with('error', 'Error al guardar Informacion Negocio.');
        }
    }
}
