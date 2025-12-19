<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Yajra\DataTables\DataTables;

class ClienteController extends Controller
{
    //index
    /* public function index(){
        $clientes = Cliente::all();
        return view('modulos.clientes.index', compact('clientes'));
    } */

    public function index(Request $request){
        if ($request->ajax()) {
            $data = Cliente::select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('acciones', function($row) {
                    $actionBtn = '<div class="d-flex">';
                    $actionBtn .= '<a href="'.route('cliente.show', $row->id).'" class="btn bg-gradient-info btn-sm mr-1" title="Ver Detalles"><i class="fas fa-eye"></i></a>';
                    $actionBtn .= '<a href="'.route('cliente.edit', $row->id).'" class="btn bg-gradient-primary btn-sm mr-1" title="Editar"><i class="fas fa-edit"></i></a>';
                    $actionBtn .= '<button type="button" class="btn bg-gradient-danger btn-sm delete-btn" data-id="'.$row->id.'" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                    $actionBtn .= '</div>';
                    return $actionBtn;
                })
                ->addColumn('activo', function($row) {
                    $checked = $row->activo ? 'checked' : '';
                    return '<div class="custom-control custom-switch toggle-estado">
                                <input role="switch" type="checkbox" class="custom-control-input toggle-activo"
                                       id="activoSwitch'.$row->id.'" '.$checked.' data-id="'.$row->id.'">
                                <label class="custom-control-label" for="activoSwitch'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('fecha_registro', function($row) {
                    return $row->created_at->format('d/m/Y h:i a');
                })
                ->addColumn('nombre_completo', function($row) {
                    return $row->nombre . ' ' . $row->apellido;
                })
                ->editColumn('activo_text', function($row) {
                    return $row->activo ? 'Activo' : 'Inactivo';
                })
                ->rawColumns(['acciones', 'activo'])
                ->make(true);
        }

        return view('modulos.clientes.index');
    }

    public function create(){

        return view('modulos.clientes.create');
    }

    /**
    * Mostrar modal de creación de cliente
    */
    public function createModal(){
        return view('modulos.clientes.partials.create-modal');
    }

    public function edit(Cliente $cliente){

        return view('modulos.clientes.edit', compact('cliente'));
    }

    public function show(Cliente $cliente){

        return view('modulos.clientes.show', compact('cliente'));
    }

    /**
    * Buscar clientes para select2 o autocomplete
    */
    public function search(Request $request){
        $term = $request->get('q', '');

        $clientes = Cliente::where('activo', 1)
            ->where(function($query) use ($term) {
                $query->where('nombre', 'LIKE', "%{$term}%")
                    ->orWhere('apellido', 'LIKE', "%{$term}%")
                    ->orWhere('rfc', 'LIKE', "%{$term}%")
                    ->orWhere('correo', 'LIKE', "%{$term}%");
            })
            ->limit(10)
            ->get()
            ->map(function($cliente) {
                return [
                    'id' => $cliente->id,
                    'text' => $cliente->nombre . ' ' . $cliente->apellido . ($cliente->rfc ? " ({$cliente->rfc})" : ''),
                    'nombre_completo' => $cliente->nombre . ' ' . $cliente->apellido,
                    'rfc' => $cliente->rfc,
                    'telefono' => $cliente->telefono,
                    'correo' => $cliente->correo
                ];
            });

        return response()->json([
            'results' => $clientes
        ]);
    }

    /**
    * Obtener estadísticas de clientes
    */
    public function stats(){
        $stats = [
            'total' => Cliente::count(),
            'activos' => Cliente::where('activo', 1)->count(),
            'inactivos' => Cliente::where('activo', 0)->count(),
            'con_rfc' => Cliente::whereNotNull('rfc')->count(),
            'con_correo' => Cliente::whereNotNull('correo')->count(),
            'registrados_hoy' => Cliente::whereDate('created_at', today())->count(),
            'registrados_este_mes' => Cliente::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count()
        ];

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        }

        return $stats;
    }

    // Método para actualizar el estado activo via AJAX
    public function toggleActivo(Request $request){
        try {
            $cliente = Cliente::findOrFail($request->id);
            $cliente->activo = $request->activo;
            $cliente->save();

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

            // Crear el cliente
            $cliente = Cliente::create($validated);

            DB::commit();

            // SI LA PETICIÓN ES AJAX (Viene del Modal del POS)
            if ($request->ajax()) {
                return response()->json($cliente);
            }

            // SI ES UNA PETICIÓN NORMAL (Viene del CRUD de clientes)
            return redirect()->route('cliente.index')->with('success', 'Cliente Creado Correctamente');

        } catch (Exception $e) {

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

            DB::rollBack();
            Log::error('Error al Actualizar Cliente:' . $e->getMessage());
            return redirect()->route('cliente.index')->with('error', 'Error al Actualizar Cliente' . $e->getMessage());
        }

    }

    /* public function destroy(Cliente $cliente){

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

    } */

    // Método para eliminar via AJAX
    public function destroy(Request $request, $id){
        try {
            $cliente = Cliente::findOrFail($id);
            $cliente->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cliente eliminado correctamente'
                ]);
            }

            return redirect()->route('cliente.index')->with('success', 'Cliente eliminado correctamente');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el cliente'
                ], 500);
            }

            return redirect()->route('cliente.index')->with('error', 'Error al eliminar el cliente');
        }
    }
}
