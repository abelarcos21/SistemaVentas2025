<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    public function store(Request $request){

        // Validación clara y separada
        $validated = $request->validate([
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'required|string|max:100',
            'rfc'       => 'required|string|size:13|unique:clientes,rfc',
            'telefono'  => 'required|string|regex:/^[0-9]{10}$/',
            'correo'    => 'required|email|max:100|unique:clientes,correo',
            'activo' => 'sometimes|boolean',//Si quieres permitir activar o desactivar clientes
            // 'activo' no es necesario porque la base de datos ya le pone true por defecto
        ]);

        DB::beginTransaction();

        try {
            //codigo
            $validated['activo'] = $request->has('activo');//Si quieres permitir activar o desactivar clientes en el formulario si el request tiene activo

            Cliente::create($validated);

            DB::commit();

            return redirect()->route('cliente.index')->with('success', 'Cliente Creado Correctamente');

        } catch (Exception $e) {
            //exception $e;
            DB::rollBack();
            Log::error('Error al Guardar Cliente:' . $e->getMessage());
            return redirect()->route('cliente.index')->with('error', 'Error al Guardar Cliente' . $e->getMessage());
        }

    }
}
