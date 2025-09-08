<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; //IMPORTANTE: esta línea importa la clase base
use App\Models\Marca;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class MarcaController extends Controller
{
    //metodo index
    public function index(Request $request){
        if ($request->ajax()) {
            $marcas = Marca::select(['id', 'nombre', 'descripcion', 'activo', 'created_at']);

            return DataTables::of($marcas)
                ->addIndexColumn()
                ->addColumn('fecha_registro', function ($marca) {
                    return $marca->created_at->format('d/m/Y h:i a');
                })
                ->addColumn('activo', function ($marca) {
                    $checked = $marca->activo ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input role="switch" type="checkbox" class="custom-control-input toggle-activo"
                                       id="activoSwitch' . $marca->id . '" ' . $checked . ' data-id="' . $marca->id . '">
                                <label class="custom-control-label" for="activoSwitch' . $marca->id . '"></label>
                            </div>';
                })
                ->addColumn('acciones', function ($marca) {
                    return '<div class="d-flex">
                                <a href="' . route('marca.show', $marca) . '" class="btn bg-gradient-info btn-sm mr-1">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <a href="' . route('marca.edit', $marca) . '" class="btn bg-gradient-warning btn-sm mr-1">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <button type="button" class="btn bg-gradient-danger btn-sm delete-btn" data-id="' . $marca->id . '">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </div>';
                })
                ->rawColumns(['activo', 'acciones'])
                ->make(true);
        }

        return view('modulos.marcas.index');
    }

    // Método para actualizar el estado activo via AJAX
    public function toggleActivo(Request $request){
        try {
            $marca = Marca::findOrFail($request->id);
            $marca->activo = $request->activo;
            $marca->save();

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado'
            ], 500);
        }
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
