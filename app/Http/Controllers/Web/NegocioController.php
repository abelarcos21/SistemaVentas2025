<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Moneda;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class NegocioController extends Controller
{
    //
    public function edit(Empresa $empresa){

        $empresa = \App\Models\Empresa::first(); // asumes solo una
        $monedas = Moneda::all(); // o puedes ordenar alfabéticamente

        return view('modulos.negocio.informacion',compact('empresa', 'monedas'));
    }

    public function perfil(){
        return view('modulos.configuracion.perfil');
    }

    public function update(Request $request){

        $empresa = \App\Models\Empresa::first();

        // Validación clara y separada
        $validated = $request->validate([

           'razon_social' => 'required|string|max:255',
           'rfc' => 'required|string|max:13|unique:empresas,rfc,' . $empresa->id,
            'telefono' => 'nullable|string|max:20',
            'correo' => 'required|email|unique:empresas,correo,' . $empresa->id,
            'moneda' => 'required|exists:monedas,codigo',
            'imagen' => 'nullable|image|max:2048',
            'direccion' => 'nullable|string',
            'regimen_fiscal' => 'nullable|string|max:5',
            'codigo_postal' => 'nullable|string|max:10',

        ]);

        DB::beginTransaction();

        try {

            if($request->hasFile('imagen')) {
                $validated['imagen'] = $request->file('imagen')->store('empresa', 'public');
            }

            // Actualiza la moneda en productos si cambió
            if ($empresa->moneda !== $validated['moneda']) {
                \App\Models\Producto::query()->update(['moneda' => $validated['moneda']]);
            }

            $empresa->update($validated); // ✅ actualiza el registro ya existente

            DB::commit();

            return redirect()->route('negocio.edit')->with('success', 'Informacion Actualizada Correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error al guardar Informacion Negocio: ' . $e->getMessage());

            return redirect()->route('negocio.edit')->with('error', 'Error al guardar Informacion Negocio.');
        }
    }
}
