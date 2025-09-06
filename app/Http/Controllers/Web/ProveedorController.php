<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
use App\Models\Proveedor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

use Exception;

class ProveedorController extends Controller
{
    //metodo index
     public function index(Request $request)
    {
        if ($request->ajax()) {
            $proveedores = Proveedor::select('*');

            return DataTables::of($proveedores)
                ->addIndexColumn()
                ->editColumn('nombre', function ($proveedor) {
                    return $proveedor->nombre;
                })
                ->editColumn('telefono', function ($proveedor) {
                    return $proveedor->telefono ?? 'N/A';
                })
                ->editColumn('email', function ($proveedor) {
                    return $proveedor->email ?? 'N/A';
                })
                ->editColumn('codigo_postal', function ($proveedor) {
                    return $proveedor->codigo_postal ?? 'N/A';
                })
                ->editColumn('sitio_web', function ($proveedor) {
                    if ($proveedor->sitio_web) {
                        $url = $proveedor->sitio_web;
                        // Agregar http:// si no tiene protocolo
                        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                            $url = "http://" . $url;
                        }
                        return '<a href="' . $url . '" target="_blank" class="text-primary">' . $proveedor->sitio_web . '</a>';
                    }
                    return 'N/A';
                })
                ->editColumn('notas', function ($proveedor) {
                    return $proveedor->notas ?
                        '<span class="text-muted">' . Str::limit($proveedor->notas, 50) . '</span>' :
                        'Sin notas';
                })
                ->editColumn('activo', function($proveedor) {
                    $checked = $proveedor->activo ? 'checked' : '';
                    return '<div class="custom-control custom-switch d-flex justify-content-center">
                                <input role="switch" type="checkbox" class="custom-control-input toggle-activo"
                                       id="activoSwitch'.$proveedor->id.'" '.$checked.' data-id="'.$proveedor->id.'">
                                <label class="custom-control-label" for="activoSwitch'.$proveedor->id.'"></label>
                            </div>';
                })
                ->addColumn('fecha_registro', function ($proveedor) {
                    return $proveedor->created_at->format('d/m/Y H:i');
                })
                ->addColumn('acciones', function ($proveedor) {
                    return '
                        <div class="btn-group" role="group">
                            <a href="' . route('proveedor.show', $proveedor->id) . '"
                               class="btn btn-outline-info btn-sm"
                               title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="' . route('proveedor.edit', $proveedor->id) . '"
                               class="btn btn-outline-primary btn-sm"
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button"
                                    class="btn btn-outline-danger btn-sm delete-btn"
                                    data-id="' . $proveedor->id . '"
                                    title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>';
                })
                ->rawColumns(['sitio_web', 'notas', 'activo', 'acciones'])
                ->make(true);
        }

        return view('modulos.proveedores.index');
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

    // Método para actualizar el estado activo via AJAX
    public function toggleActivo(Request $request){
        try {
            $proveedor = Proveedor::findOrFail($request->id);
            $proveedor->activo = $request->activo;
            $proveedor->save();

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

    // Método para eliminar via AJAX
    /* public function destroy($id)
    {
        try {
            $proveedor = Proveedor::findOrFail($id);
            $proveedor->delete();

            return response()->json([
                'success' => true,
                'message' => 'Proveedor eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el proveedor: ' . $e->getMessage()
            ], 500);
        }
    } */
}
