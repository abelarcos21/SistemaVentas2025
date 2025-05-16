<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProveedorController extends Controller
{
    //metodo index
    public function index(){

        $proveedores = Proveedor::all();

        return view('modulos.proveedores.index', compact('proveedores'));

    }

    public function create(){

        return view('modulos.proveedores.create');

    }

    public function show(Proveedor $proveedor){

    }

    public function edit(Proveedor $proveedor){

        return view('modulos.proveedores.edit', compact('proveedor'));

    }

    public function store(Request $request){

        // Validación clara y separada
        $validated = $request->validate([
            'nombre'        => 'required|string|max:255',
            'telefono'      => 'required|string|max:255',
            'email'         => 'required|string|max:255|email',
            'codigo_postal' => 'required|string|max:20',
            'sitio_web'     => 'required|string|max:255|url',
            'notas'         => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {

            Proveedor::create($validated);

            DB::commit();

            return redirect()->route('proveedor.index')->with('success', 'Proveedor creado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error al guardar proveedor: ' . $e->getMessage());

            return redirect()->route('proveedor.index')->with('error', 'Error al guardar proveedor.');
        }
    }

    public function update(Request $request, Proveedor $proveedor){

        try{

            $validated = $request->validate([

                'nombre' => 'required|string|max:255',
                'telefono' => 'required|string|max:255',
                'email' => 'required|string|max:255',
                'codigo_postal' => 'required|string|max:255',
                'sitio_web' => 'required|string|max:255',
                'notas' => 'required|string|max:255',

            ]);

            $proveedor->fill($validated); // metodo fill es igual que el método save() pero sin crear un nuevo registro

            $proveedor->save();


            return redirect()->route('proveedor.index')->with('success', 'Proveedor Actualizado Correctamente');

        }catch(Exception $e){

            return redirect()->route('proveedor.index')->with('error', 'Error al Guardar!' . $e->getMessage());
        }

    }

    public function destroy(Proveedor $proveedor){

        $nombreProveedor = $proveedor->nombre;
        $proveedor->delete();
        return redirect()->route('proveedor.index')->with('success','El Proveedor  '.$nombreProveedor.'  se Elimino');

    }
}
