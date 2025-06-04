<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ClienteController extends Controller
{
    //index
    public function index(){
        $clientes = Cliente::all();
        return view('modulos.clientes.index', compact('clientes'));
    }

    public function create(){

        return view('modulos.clientes.create');
    }

    public function edit(Cliente $cliente){

        return view('modulos.clientes.edit', compact('cliente'));
    }

    public function show(Cliente $cliente){

        return view('modulos.clientes.show', compact('cliente'));
    }

    public function store(Request $request){

        // Validar datos
        $validated = $request->validate([
            'nombre'    => ['required', 'string', 'max:100'],
            'apellido'  => ['required', 'string', 'max:100'],
            'rfc'       => ['required', 'string', 'size:13', 'unique:clientes,rfc'],
            'telefono'  => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'correo'    => ['required', 'email', 'max:100', 'unique:clientes,correo'],
            'activo'    => ['required', 'boolean'],// gracias al input hidden + checkbox, este campo siempre se enviará
        ]);

        DB::beginTransaction();

        try {
            // Crear nuevo cliente
            Cliente::create($validated);// requiere tener $fillable en el modelo

            DB::commit();

            return redirect()->route('cliente.index')->with('success', 'Cliente Creado Correctamente');

        } catch (Exception $e) {
            //exception $e;
            DB::rollBack();
            Log::error('Error al Guardar Cliente:' . $e->getMessage());
            return redirect()->route('cliente.index')->with('error', 'Error al Guardar Cliente' . $e->getMessage());
        }

    }

    public function update(Request $request, Cliente $cliente){

        // Validar datos
        $validated = $request->validate([
            'nombre'    => ['required', 'string', 'max:100'],
            'apellido'  => ['required', 'string', 'max:100'],
            'rfc'       => ['required', 'string', 'size:13', 'unique:clientes,rfc,' . $cliente->id],
            'telefono'  => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'correo'    => ['required', 'email', 'max:100', 'unique:clientes,correo,' . $cliente->id],
            'activo'    => ['required', 'boolean'],
        ]);


        DB::beginTransaction();

        try {

            //codigo...
            $cliente->fill($validated)->save();// Llenar el modelo con los datos validados y guardar

            DB::commit();

            return redirect()->route('cliente.index')->with('success', 'Cliente Actualizado Correctamente');
        } catch (Exception $e) {
            //Exception $e;
            DB::rollBack();
            Log::error('Error al Actualizar Cliente:' . $e->getMessage());
            return redirect()->route('cliente.index')->with('error', 'Error al Actualizar Cliente' . $e->getMessage());
        }

    }

    public function destroy(Cliente $cliente){

        DB::beginTransaction();

        try {

            //codigo
            $nombreCliente = $cliente->nombre;
            $cliente->delete();

            DB::commit();

            return redirect()->route('cliente.index')->with('success','El Cliente  '.$nombreCliente.'  se Elimino');

        } catch (Exception $e) {
            //exception $e;
            DB::rollBack();

            Log::error('Error al Eliminar Cliente:' . $e->getMessage());
            return redirect()->route('cliente.index')->with('error','Error al Eliminar Cliente');

        }

    }
}
