<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
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

        return view('modulos.proveedores.show', compact('proveedor'));

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

        try{

            $proveedor->fill($validated); // metodo fill es igual que el método save() pero sin crear un nuevo registro
            $proveedor->save();

            DB::commit();

            return redirect()->route('proveedor.index')->with('success', 'Proveedor Actualizado Correctamente');

        }catch(Exception $e){

            DB::rollBack();

            Log::error('Error al Actualizar Proveedor: ' . $e->getMessage());

            return redirect()->route('proveedor.index')->with('error', 'Error al Actualizar Proveedor!' . $e->getMessage());
        }

    }

    public function destroy(Proveedor $proveedor){

        DB::beginTransaction();

        try {

            //codigo
            $nombreProveedor = $proveedor->nombre;
            $proveedor->delete();

            DB::commit();

            return redirect()->route('proveedor.index')->with('success','El Proveedor  '.$nombreProveedor.'  se Elimino');

        } catch (Exception $e) {
            //exception $e;
            DB::rollBack();

            Log::error('Error al Eliminar Proveedor:' . $e->getMessage());
            return redirect()->route('proveedor.index')->with('error','Error al Eliminar Proveedor');

        }
    }
}
