<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; //IMPORTANTE: esta línea importa la clase base
use App\Models\Categoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class CategoriaController extends Controller{

    //metodo index
    public function index(Request $request){
        if ($request->ajax()) {
            $categorias = Categoria::with('user')
                ->select('categorias.*');

            return DataTables::of($categorias)
                ->addIndexColumn()
                ->editColumn('created_at', function ($categoria) {
                    return $categoria->created_at->format('d/m/Y h:i a');
                })
                ->editColumn('activo', function ($categoria) {
                    $checked = $categoria->activo ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input role="switch" type="checkbox" class="custom-control-input toggle-activo"
                                       id="activoSwitch' . $categoria->id . '" ' . $checked . '
                                       data-id="' . $categoria->id . '">
                                <label class="custom-control-label" for="activoSwitch' . $categoria->id . '"></label>
                            </div>';
                })
                ->addColumn('acciones', function ($categoria) {
                    return '<div class="d-flex">
                                <a href="' . route('categoria.show', $categoria) . '" class="btn bg-gradient-info btn-sm mr-1" title="Ver Detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="' . route('categoria.edit', $categoria) . '" class="btn bg-gradient-primary btn-sm mr-1" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="' . route('categoria.destroy', $categoria) . '" method="POST" class="formulario-eliminar" style="display:inline;">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn bg-gradient-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>';
                })
                ->rawColumns(['activo', 'acciones'])
                ->make(true);
        }

        return view('modulos.categorias.index');
    }

    // Método para actualizar el estado activo via AJAX
    public function toggleActivo(Request $request){
        try {
            $categoria = Categoria::findOrFail($request->id);
            $categoria->activo = $request->activo;
            $categoria->save();

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
        return view('modulos.categorias.create');

    }

    public function store(Request $request){

        //validar datos
        $validated = $request->validate([

            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',// gracias al input hidden + checkbox, este campo siempre se enviará

        ]);

        DB::beginTransaction();

        try{

            $validated['user_id'] = Auth::user()->id;

            Categoria::create($validated);

            DB::commit();

            return redirect()->route('categoria.index')->with('success', 'Categoria Creada Correctamente');

        }catch(Exception $e){

            DB::rollBack();
            Log::error('Error al Guardar Categoria:' . $e->getMessage());
            return redirect()->route('categoria.index')->with('error', 'Error al Guardar Categoria' . $e->getMessage());
        }

    }

    public function show(Categoria $categoria){
        return view('modulos.categorias.show', compact('categoria'));
    }

    public function edit(Categoria $categoria){
        return view('modulos.categorias.edit', compact('categoria'));

    }


    public function update(Request $request, Categoria $categoria){

        //validar datos
        $validated = $request->validate([

            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',// gracias al input hidden + checkbox, este campo siempre se enviará

        ]);

        DB::beginTransaction();

        try {

            // Llenar el modelo con los datos validados y guardar
            $categoria->fill($validated)->save(); // metodo fill es igual que el método save() pero sin crear un nuevo registro

            DB::commit();

            return redirect()->route('categoria.index')->with('success', 'Categoria Actualizada Correctamente');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al Actualizar Categoria:' . $e->getMessage());
            return redirect()->route('categoria.index')->with('error', 'Error al Actualizar Categoria' . $e->getMessage());

        }

    }

    public function destroy(Categoria $categoria){

        DB::beginTransaction(); //  Inicia transacción

        try {

            //$categoria = Categoria::findOrFail($id); //no es nesesario lo inyecto modelbinding
            $nombreCategoria = $categoria->nombre;
            $categoria->delete(); //  Si falla aquí (por clave foránea), lanzará excepción

            DB::commit(); //  Si todo sale bien, confirma transacción

            return redirect()->route('categoria.index')->with('success','La Categoria  '.$nombreCategoria.'  se Elimino');

        } catch (QueryException $e) {

            DB::rollBack(); //  Revierte los cambios

            if($e->getCode() == 23000){
                // Error por clave foránea (productos asociados)
                return redirect()->route('categoria.index')->with('error','No se puede eliminar la categoria porque está asociada a uno o más productos.');
            }

            return redirect()->route('categoria.index')->with('error','Ocurrió un error al eliminar la categoria.');

        }catch (Exception $e){
            DB::rollBack(); // Por cualquier otra excepción
            return redirect()->route('categoria.index')->with('error','Ocurrió un error inesperado: ' . $e->getMessage());
        }

    }

}
