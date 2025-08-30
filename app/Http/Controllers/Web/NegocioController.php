<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Moneda;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class NegocioController extends Controller
{
    //
    public function edit(Empresa $empresa){

        $empresa = Empresa::with('moneda')->first(); // asumes solo una
        $monedas = Moneda::all(); // o puedes ordenar alfabéticamente

        return view('modulos.negocio.informacion',compact('empresa', 'monedas'));
    }

    public function perfil(){
        return view('modulos.configuracion.perfil');
    }

    public function update(Request $request){

        $empresa = Empresa::first();

        // Validación clara y separada
        $validated = $request->validate([

            'razon_social' => 'required|string|max:255',
            'rfc' => 'required|string|max:13|unique:empresas,rfc,' . $empresa->id,
            'telefono' => 'nullable|string|max:20',
            'correo' => 'required|email|unique:empresas,correo,' . $empresa->id,
            'moneda_id' => 'required|exists:monedas,id',
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

            // Verificar si cambió la moneda y actualizar productos
            if ($empresa->moneda_id !== $validated['moneda_id']) {
                // Actualizar todos los productos con la nueva moneda
                Producto::query()->update(['moneda_id' => $validated['moneda_id']]);

                // Log del cambio
                $monedaAnterior = $empresa->moneda?->codigo ?? 'N/A';
                $monedaNueva = Moneda::find($validated['moneda_id'])?->codigo ?? 'N/A';
                Log::info("Cambio de moneda empresa: {$monedaAnterior} -> {$monedaNueva}");
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
