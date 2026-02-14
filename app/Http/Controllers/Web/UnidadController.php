<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unidad;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class UnidadController extends Controller
{

    public function index(Request $request){
        // Si es AJAX (DataTables)
        if ($request->ajax()) {
            $unidades = Unidad::withCount('productos')
                ->select('unidades.*');

            return DataTables::of($unidades)
                ->addIndexColumn()
                ->addColumn('nombre_completo', function ($unidad) {
                    return $unidad->nombre_completo;
                })
                ->addColumn('tipo_badge', function ($unidad) {
                    return $unidad->tipo_badge;
                })
                ->addColumn('productos_count', function ($unidad) {
                    $count = $unidad->productos_count;
                    $badge = $count > 0 ? 'badge-success' : 'badge-secondary';
                    return '<span class="badge ' . $badge . '">' . $count . ' productos</span>';
                })
                ->addColumn('permite_decimales_badge', function ($unidad) {
                    if ($unidad->permite_decimales) {
                        return '<span class="badge badge-info"><i class="fas fa-check"></i> Decimales</span>';
                    }
                    return '<span class="badge badge-secondary"><i class="fas fa-times"></i> Enteros</span>';
                })
                ->addColumn('estado', function ($unidad) {
                    if ($unidad->activo) {
                        return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>';
                    }
                    return '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';
                })
                ->addColumn('actions', function ($unidad) {
                    $canDelete = !$unidad->tieneProductos();

                    // Pasamos todos los datos ocultos como atributos data-*
                    $showBtn = '<button type="button" class="btn btn-secondary btn-sm mr-1 btn-ver-detalles"
                        title="Ver Detalles"
                        data-nombre="'.htmlspecialchars($unidad->nombre).'"
                        data-codigo="'.$unidad->factor_conversion.'"
                        data-categoria="'.$unidad->nombre_categoria.'"
                        data-unidad="'.$unidad->nombre_unidad.'"
                        data-marca="'.$unidad->nombre_marca.'"
                        data-proveedor="'.$unidad->nombre_proveedor.'"
                        data-descripcion="'.htmlspecialchars($unidad->descripcion).'"
                        data-stock="'.$unidad->cantidad.'"
                        data-pventa="'.number_format($unidad->precio_venta, 2).'"
                        data-pcompra="'.number_format($unidad->precio_compra, 2).'"
                        data-pmayoreo="'.($unidad->permite_mayoreo ? number_format($unidad->precio_mayoreo, 2) : 'N/A').'"
                        data-poferta="'.($unidad->en_oferta ? number_format($unidad->precio_oferta, 2) : 'N/A').'"
                        data-moneda="'.($unidad->moneda->codigo ?? '$').'"
                        data-fechareg="'.$unidad->created_at->format('d/m/Y').'"

                    >
                        <i class="fas fa-eye"></i>
                    </button>';

                    $editBtn = '<button type="button" class="btn btn-info btn-sm mr-1 btn-edit d-flex align-items-center" title="Editar Unidad"
                                    data-id="'.$unidad->id.'">
                                    <i class="fas fa-edit "></i>
                                </button>';


                    $deleteBtn = $canDelete
                        ? '<button type="button" class="btn btn-sm btn-danger btn-delete"
                               data-id="' . $unidad->id . '"
                               data-nombre="' . $unidad->nombre . '"
                               title="Eliminar">
                               <i class="fas fa-trash"></i>
                           </button>'
                        : '<button type="button" class="btn btn-sm btn-secondary"
                               title="No se puede eliminar (tiene productos)"
                               disabled>
                               <i class="fas fa-lock"></i>
                           </button>';

                    return '<div class="btn-group" role="group">'. $showBtn . ' ' . $editBtn . ' ' . $deleteBtn . '</div>';
                })
                ->rawColumns(['tipo_badge', 'productos_count', 'permite_decimales_badge', 'estado', 'actions'])
                ->make(true);
        }

        // Vista normal
        return view('modulos.unidades.index');
    }

    public function store(Request $request){
        // Validación
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:unidades,nombre',
            'abreviatura' => 'required|string|max:10|unique:unidades,abreviatura',
            'codigo_sat' => 'nullable|string|max:10',
            'tipo' => 'required|in:peso,volumen,longitud,pieza,tiempo,otro',
            'factor_conversion' => 'nullable|numeric|min:0',
            'unidad_base' => 'nullable|string|max:50',
            'permite_decimales' => 'boolean',
            'activo' => 'boolean',
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe una unidad con ese nombre.',
            'abreviatura.required' => 'La abreviatura es obligatoria.',
            'abreviatura.unique' => 'Ya existe una unidad con esa abreviatura.',
            'tipo.required' => 'El tipo es obligatorio.',
            'tipo.in' => 'El tipo seleccionado no es válido.',
        ]);

        DB::beginTransaction();

        try {
            $unidad = Unidad::create([
                'nombre' => $validated['nombre'],
                'abreviatura' => $validated['abreviatura'],
                'codigo_sat' => $validated['codigo_sat'] ?? null,
                'tipo' => $validated['tipo'],
                'factor_conversion' => $validated['factor_conversion'] ?? null,
                'unidad_base' => $validated['unidad_base'] ?? null,
                'permite_decimales' => $request->boolean('permite_decimales'),
                'activo' => $request->boolean('activo', true),
                'descripcion' => $validated['descripcion'] ?? null,
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Unidad creada exitosamente.',
                    'unidad' => $unidad
                ]);
            }

            return redirect()->route('unidad.index')
                ->with('success', 'Unidad creada exitosamente.');

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error al crear unidad: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la unidad: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withErrors(['error' => 'Error al crear la unidad.'])
                ->withInput();
        }
    }

    /* public function show(Unidad $unidad){
        $unidad->load('productos');

        return response()->json([
            'success' => true,
            'unidad' => $unidad
        ]);
    } */

    public function createModal(){

        return view('modulos.unidades.partials.create-modal');
    }

    public function editModal($id){

        // Debug temporal
        //\Log::info('ID recibido en editModal: ' . $id);
        $unidad = Unidad::findOrFail($id);
        //\Log::info('unidad recibido en editModal: ' . $unidad);

        return view('modulos.unidades.partials.edit-modal', compact('unidad'));

    }

    public function deleteModal(){

    }

    public function update(Request $request, Unidad $unidad){
        // Validación
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:unidades,nombre,' . $unidad->id,
            'abreviatura' => 'required|string|max:10|unique:unidades,abreviatura,' . $unidad->id,
            'codigo_sat' => 'nullable|string|max:10',
            'tipo' => 'required|in:peso,volumen,longitud,pieza,tiempo,otro',
            'factor_conversion' => 'nullable|numeric|min:0',
            'unidad_base' => 'nullable|string|max:50',
            'permite_decimales' => 'boolean',
            'activo' => 'boolean',
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe otra unidad con ese nombre.',
            'abreviatura.required' => 'La abreviatura es obligatoria.',
            'abreviatura.unique' => 'Ya existe otra unidad con esa abreviatura.',
            'tipo.required' => 'El tipo es obligatorio.',
            'tipo.in' => 'El tipo seleccionado no es válido.',
        ]);

        DB::beginTransaction();

        try {
            $unidad->update([
                'nombre' => $validated['nombre'],
                'abreviatura' => $validated['abreviatura'],
                'codigo_sat' => $validated['codigo_sat'] ?? null,
                'tipo' => $validated['tipo'],
                'factor_conversion' => $validated['factor_conversion'] ?? null,
                'unidad_base' => $validated['unidad_base'] ?? null,
                'permite_decimales' => $request->boolean('permite_decimales'),
                'activo' => $request->boolean('activo'),
                'descripcion' => $validated['descripcion'] ?? null,
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Unidad actualizada exitosamente.',
                    'unidad' => $unidad
                ]);
            }

            return redirect()->route('unidad.index')
                ->with('success', 'Unidad actualizada exitosamente.');

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar unidad: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la unidad: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withErrors(['error' => 'Error al actualizar la unidad.'])
                ->withInput();
        }
    }

    public function destroy(Unidad $unidad){
        try {
            // Verificar si tiene productos
            if ($unidad->tieneProductos()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la unidad porque tiene productos asociados.'
                ], 422);
            }

            $nombre = $unidad->nombre;
            $unidad->delete();

            return response()->json([
                'success' => true,
                'message' => "Unidad '{$nombre}' eliminada exitosamente."
            ]);

        } catch (Exception $e) {
            Log::error('Error al eliminar unidad: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la unidad: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleEstado(Unidad $unidad){
        try {
            $unidad->update([
                'activo' => !$unidad->activo
            ]);

            $estado = $unidad->activo ? 'activada' : 'desactivada';

            return response()->json([
                'success' => true,
                'message' => "Unidad {$estado} exitosamente.",
                'activo' => $unidad->activo
            ]);

        } catch (Exception $e) {
            Log::error('Error al cambiar estado de unidad: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado.'
            ], 500);
        }
    }

    //Obtener unidades para select2
    public function obtenerParaSelect(Request $request){
        $termino = $request->get('q');

        $unidades = Unidad::activas()
            ->when($termino, function ($query, $termino) {
                return $query->buscar($termino);
            })
            ->orderBy('nombre')
            ->limit(50)
            ->get(['id', 'nombre', 'abreviatura']);

        $resultados = $unidades->map(function ($unidad) {
            return [
                'id' => $unidad->id,
                'text' => $unidad->nombre_completo
            ];
        });

        return response()->json([
            'results' => $resultados
        ]);
    }

}
