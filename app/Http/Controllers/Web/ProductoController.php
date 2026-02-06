<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller; //IMPORTANTE: esta línea importa la clase base
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Imagen;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Marca;
use App\Models\Unidad;
use Exception;
use Storage;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\DataTables;
use App\Imports\ProductosImport; //IMPORTANTE: para importar productos masivamente
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;



class ProductoController extends Controller
{
    //constructor
    function __construct(){
        $this->middleware('permission:productos.index|productos.create|productos.edit|productos.destroy', ['only' => ['index','show']]);
        $this->middleware('permission:productos.create', ['only' => ['create','store']]);
        $this->middleware('permission:productos.edit', ['only' => ['edit','update']]);
        $this->middleware('permission:productos.destroy', ['only' => ['destroy']]);
    }

    //index
    public function index(Request $request){

        // Si es una petición AJAX (DataTables), devolver JSON
        if(request()->ajax()) {
            return $this->getDataTableData($request);//pasar el $request
        }
        // Si es una petición normal, devolver la vista
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();
        $marcas = Marca::all();

        return view('modulos.productos.index', compact('categorias', 'proveedores', 'marcas'));
    }

    private function getDataTableData(Request $request = null){

        // Si no se pasa request, obtenerlo de la función helper
        if (!$request) {
            $request = request();
        }

        $productos = Producto::select(
            'productos.id',
            'productos.nombre',
            'productos.requiere_fecha_caducidad',
            'productos.fecha_caducidad',
            'productos.descripcion',
            'productos.precio_compra',
            'productos.precio_venta',
            'productos.cantidad',
            'productos.moneda_id',
            'productos.codigo',
            'productos.barcode_path',
            'productos.categoria_id',
            'productos.unidad_id',
            'productos.proveedor_id',
            'productos.marca_id',
            'productos.created_at',
            'productos.activo',
            'productos.permite_mayoreo',
            'productos.en_oferta',
            'productos.precio_mayoreo',
            'productos.precio_oferta',
            'productos.cantidad_minima_mayoreo',
            'productos.fecha_inicio_oferta',
            'productos.fecha_fin_oferta',
            'categorias.nombre as nombre_categoria',
            'unidades.nombre as nombre_unidad',
            'proveedores.nombre as nombre_proveedor',
            'marcas.nombre as nombre_marca',
            'imagens.ruta as imagen_producto',
            'imagens.id as imagen_id'
        )
        ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
        ->join('unidades', 'productos.unidad_id', '=', 'unidades.id')
        ->join('proveedores', 'productos.proveedor_id', '=', 'proveedores.id')
        ->join('marcas', 'productos.marca_id', '=', 'marcas.id')
        ->leftJoin('imagens', 'productos.id', '=', 'imagens.producto_id')
        ->with('moneda');

        // APLICAR FILTRO DE CADUCIDAD
        $filterCaducidad = $request->get('filter_caducidad', 'all');

        switch ($filterCaducidad) {
            case 'vencidos':
                $productos->where('requiere_fecha_caducidad', true)
                      ->whereNotNull('fecha_caducidad')
                      ->where('fecha_caducidad', '<', now());
                break;

            case '7dias':
                $productos->where('requiere_fecha_caducidad', true)
                      ->whereNotNull('fecha_caducidad')
                      ->whereBetween('fecha_caducidad', [now(), now()->addDays(7)]);
                break;

            case '15dias':
                $productos->where('requiere_fecha_caducidad', true)
                      ->whereNotNull('fecha_caducidad')
                      ->whereBetween('fecha_caducidad', [now(), now()->addDays(15)]);
                break;

            case '30dias':
                $productos->where('requiere_fecha_caducidad', true)
                      ->whereNotNull('fecha_caducidad')
                      ->whereBetween('fecha_caducidad', [now(), now()->addDays(30)]);
                break;

            case 'all':
            default:
                // No aplicar filtro
                break;
        }


        return DataTables::of($productos)
            /* ->addIndexColumn() */
            ->addColumn('boton_compra', function ($producto) {
                if ($producto->cantidad == 0) {
                    return '<button type="button" class="btn btn-success btn-sm mr-1 btn-compra d-flex align-items-center"
                                data-id="'.$producto->id.'">
                                <i class="fas fa-shopping-cart mr-1"></i> 1.ª Compra
                            </button>';
                } else {
                    return '<button type="button" class="btn btn-primary btn-sm mr-1 btn-compra d-flex align-items-center"
                                data-id="'.$producto->id.'">
                                <i class="fas fa-plus mr-1"></i> Reabastecer
                            </button>';
                }
            })
            ->addColumn('imagen', function($producto){
                $ruta = $producto->imagen && $producto->imagen->ruta
                ? asset('storage/' . $producto->imagen->ruta)
                : asset('images/placeholder-caja.png');

                return '
                    <a href="#" data-toggle="modal" data-target="#modalImagen'.$producto->id.'">
                        <img src="'.$ruta.'" width="50" height="50"
                            class="img-thumbnail rounded shadow"
                            style="object-fit: cover;">
                    </a>

                    <div class="modal fade" id="modalImagen'.$producto->id.'"
                        tabindex="-1"
                        role="dialog" aria-labelledby="modalLabel'.$producto->id.'" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content bg-white">
                                <div class="modal-header bg-gradient-info">
                                    <h5 class="modal-title" id="modalLabel'.$producto->id.'">Imagen de '.$producto->nombre.'</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="'.$ruta.'" class="img-fluid rounded shadow">
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            })
            // Mostrar precio base con moneda
            ->addColumn('precio_base', function ($producto) {
                $precioVenta = ($producto->moneda->codigo ?? '') . ' ' . number_format($producto->precio_venta, 2);
                return '<span class="text-primary fw-bold">' . $precioVenta . '</span>';
            })
            // Mostrar mayoreo si aplica
           /*  ->addColumn('mayoreo', function ($producto) {
                if ((int)$producto->permite_mayoreo == true && $producto->precio_mayoreo > 0) {
                    return '<span class="badge bg-info">'
                        . ($producto->moneda->codigo ?? '') . ' '
                        . number_format($producto->precio_mayoreo, 2)
                        . ' (min. ' . $producto->cantidad_minima_mayoreo . ')'
                        . '</span>';
                }
                return '<span class="badge bg-secondary">N/A</span>';
            }) */
            // Mostrar oferta si aplica
           /*  ->addColumn('oferta', function ($producto) {
                if ((int)$producto->en_oferta == true && $producto->precio_oferta > 0) {
                    $hoy = now();
                    $inicio = \Carbon\Carbon::parse($producto->fecha_inicio_oferta);
                    $fin = \Carbon\Carbon::parse($producto->fecha_fin_oferta);

                    if ($hoy->between($inicio, $fin)) {
                        // Oferta vigente
                        return '<span class="badge bg-success">'
                            . ($producto->moneda->codigo ?? '') . ' '
                            . number_format($producto->precio_oferta, 2)
                            . ' (Vigente: ' . $inicio->format('d/m/Y') . ' - ' . $fin->format('d/m/Y') . ')'
                            . '</span>';
                    } elseif ($hoy->lt($inicio)) {
                        // Oferta programada
                        return '<span class="badge bg-warning">'
                            . 'Programada (inicia: ' . $inicio->format('d/m/Y') . ')'
                            . '</span>';
                    } elseif ($hoy->gt($fin)) {
                        // Oferta vencida
                        return '<span class="badge bg-danger">'
                            . 'Vencida (terminó: ' . $fin->format('d/m/Y') . ')'
                            . '</span>';
                    }
                }
                return '<span class="badge bg-secondary">N/A</span>';
            }) */
            ->addColumn('caducidad', function ($producto) {
                // 🔍 DEBUG temporal - ver en la consola del navegador
                /* \Log::info('Producto: ' . $producto->nombre, [
                    'requiere_caducidad' => $producto->requiere_fecha_caducidad,
                    'fecha_caducidad' => $producto->fecha_caducidad,
                    'tipo' => gettype($producto->fecha_caducidad),
                ]); */

                if (!$producto->requiere_fecha_caducidad) {
                    return '<span class="badge badge-secondary">
                                <i class="fas fa-times"></i> No aplica
                            </span>';
                }

                // Verificar que fecha_caducidad no sea null
                if (empty($producto->fecha_caducidad)) {
                    return '<span class="badge badge-warning">
                                <i class="fas fa-exclamation-triangle"></i> Sin fecha
                            </span>';
                }

                if ($producto->estaVencido()) {
                    return '<span class="badge badge-danger" title="Producto vencido">
                                <i class="fas fa-times-circle"></i> VENCIDO
                            </span><br>
                            <small class="text-danger">' .
                                $producto->fecha_caducidad->format('d/m/Y') .
                            '</small>';
                }

                $dias = $producto->diasParaVencer();
                $badgeClass = $producto->getBadgeCaducidad();

                $icono = 'fa-clock';
                if ($dias <= 7) {
                    $icono = 'fa-exclamation-triangle';
                } elseif ($dias <= 15) {
                    $icono = 'fa-exclamation-circle';
                }

                return '<span class="badge ' . $badgeClass . '" title="' . $dias . ' días restantes">
                            <i class="fas ' . $icono . '"></i> ' . $dias . ' días
                        </span><br>
                        <small class="text-muted">' .
                            $producto->fecha_caducidad->format('d/m/Y') .
                        '</small>';
            })
            ->addColumn('precio_compra_formatted', function ($producto) {
                $precioCompra = ($producto->moneda->codigo ?? '') . ' ' . number_format($producto->precio_compra, 2);
                return '<span class="text-primary fw-bold">' . $precioCompra . '</span>';
            })
            ->addColumn('cantidad', function ($producto) {
                $class = $producto->cantidad > 10 ? 'success' : ($producto->cantidad > 0 ? 'warning' : 'danger');
                $text = $producto->cantidad > 10 ? 'En stock' : ($producto->cantidad > 0 ? 'Poco stock' : 'Sin stock');
                return '<span class="badge badge-' . $class . '">' . $text . ' (' . $producto->cantidad . ')</span>';
            })
            ->addColumn('fecha_registro', function ($producto) {
                    return $producto->created_at->format('d/m/Y h:i a');
            })
            ->addColumn('activo', function ($producto) {
                $checked = $producto->activo ? 'checked' : '';
                return '<div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input toggle-status"
                                id="status' . $producto->id . '"
                                data-id="' . $producto->id . '" ' . $checked . '>
                        <label class="custom-control-label" for="status' . $producto->id . '"></label>
                        </div>';
            })
            ->addColumn('acciones', function ($producto) {
                $buttons = '<div class="d-flex">';

                // Pasamos todos los datos ocultos como atributos data-*
                $buttons .= '<button type="button" class="btn btn-secondary btn-sm mr-1 btn-ver-detalles"
                    title="Ver Detalles"
                    data-nombre="'.htmlspecialchars($producto->nombre).'"
                    data-codigo="'.$producto->codigo.'"
                    data-categoria="'.$producto->nombre_categoria.'"
                    data-unidad="'.$producto->nombre_unidad.'"
                    data-marca="'.$producto->nombre_marca.'"
                    data-proveedor="'.$producto->nombre_proveedor.'"
                    data-descripcion="'.htmlspecialchars($producto->descripcion).'"
                    data-stock="'.$producto->cantidad.'"
                    data-pventa="'.number_format($producto->precio_venta, 2).'"
                    data-pcompra="'.number_format($producto->precio_compra, 2).'"
                    data-pmayoreo="'.($producto->permite_mayoreo ? number_format($producto->precio_mayoreo, 2) : 'N/A').'"
                    data-poferta="'.($producto->en_oferta ? number_format($producto->precio_oferta, 2) : 'N/A').'"
                    data-moneda="'.($producto->moneda->codigo ?? '$').'"
                    data-fechareg="'.$producto->created_at->format('d/m/Y').'"
                    data-imagen="'.($producto->imagen ? asset('storage/' . $producto->imagen->ruta) : asset('images/placeholder-caja.png')).'"
                >
                    <i class="fas fa-eye"></i>
                </button>';

                if(auth()->user()->can('productos.edit')) {
                    $buttons .= '<button type="button" class="btn btn-info btn-sm mr-1 btn-edit d-flex align-items-center" title="Editar Producto"
                                    data-id="'.$producto->id.'">
                                    <i class="fas fa-edit mr-1"></i>
                                </button>';
                }

                if(auth()->user()->can('productos.destroy')) {
                    $buttons .= '<button data-id="'.$producto->id.'"
                                    class="btn btn-danger btn-delete btn-sm mr-1 d-flex align-items-center"  title="Eliminar Producto">
                                    <i class="fas fa-trash-alt mr-1"></i>
                                </button>';
                }

                $buttons .= '</div>';
                return $buttons;
            })
            ->editColumn('nombre', function ($producto) {
                return '<strong>' . $producto->nombre . '</strong><br>';
            })
            ->editColumn('codigo', function ($producto) {
                return '<code>' . $producto->codigo . '</code><br>';
            })
            ->rawColumns(['imagen', 'cantidad', 'activo', 'acciones', 'nombre', 'boton_compra', 'codigo','precio_base','precio_compra_formatted','oferta','mayoreo','caducidad',])
            ->make(true);
    }

    //para obtener datos de json de un producto al agregar al carrito
    public function datos($id){
        $producto = \App\Models\Producto::with('moneda')->findOrFail($id);

        // Determinar si la oferta sigue vigente
        $ofertaVigente = false;
        if ($producto->en_oferta && $producto->fecha_inicio_oferta && $producto->fecha_fin_oferta) {
            $hoy = now();
            $ofertaVigente = $hoy->between($producto->fecha_inicio_oferta, $producto->fecha_fin_oferta);
        }

        return response()->json([
            'id'                 => $producto->id,
            'nombre'             => $producto->nombre,
            'stock'              => $producto->cantidad,
            'precio_base'        => $producto->precio_venta,
            'precio_mayoreo'     => $producto->precio_mayoreo,
            'cantidad_mayoreo'   => $producto->cantidad_minima_mayoreo,
            'en_oferta'          => $producto->en_oferta,
            'precio_oferta'      => $producto->precio_oferta,
            'fecha_inicio'       => $producto->fecha_inicio_oferta,
            'fecha_fin'          => $producto->fecha_fin_oferta,
            'oferta_vigente'     => $ofertaVigente,
            'moneda'             => $producto->moneda->codigo ?? null,
            'imagen'             => $producto->imagen
                                        ? asset('storage/' . $producto->imagen->ruta)
                                        : asset('images/placeholder-caja.png'),
        ]);
    }


    public function create(){
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();
        $marcas = Marca::all();

        return view('modulos.productos.create', compact('categorias', 'proveedores','marcas'));

    }

    public function createModal(){
        $categorias = Categoria::where('activo', 1)->orderBy('nombre')->get();
        $proveedores = Proveedor::where('activo', 1)->orderBy('nombre')->get();
        $marcas = Marca::where('activo', 1)->orderBy('nombre')->get();
        $unidades = Unidad::where('activo', 1)->orderBy('nombre')->get();

        return view('modulos.productos.partials.create-modal', compact('categorias', 'proveedores', 'marcas', 'unidades'));
    }

    public function show(Producto $producto){


        $producto = Producto::select(
            'productos.*',
            'categorias.nombre as nombre_categoria',
            'proveedores.nombre as nombre_proveedor'
        )
        ->join('categorias', 'productos.categoria_id', '=' , 'categorias.id')
        ->join('proveedores', 'productos.proveedor_id', '=' , 'proveedores.id')
        ->where('productos.id', $producto->id)
        ->first();
        return view('modulos.productos.show', compact('producto'));

        /* $producto->load(['categoria', 'proveedor']);//cargar relacion eloquent

        //y en la vista aceder asi con eloquent
        {{ $producto->categoria->nombre }}
        {{ $producto->proveedor->nombre }} */

    }

    public function edit(Producto $producto){
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();
        $marcas = Marca::all();
        return view('modulos.productos.edit', compact('producto','categorias','proveedores','marcas'));

    }

    // Para manejar el modal de editar
    public function editModal($id){
        // Debug temporal
        //\Log::info('ID recibido en editModal: ' . $id);

        $producto = Producto::with(['imagen'])->findOrFail($id);
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $marcas = Marca::where('activo', true)->orderBy('nombre')->get();
        $unidades = Unidad::where('activo', 1)->orderBy('nombre')->get();

        return view('modulos.productos.partials.edit-modal', compact('producto', 'categorias', 'proveedores', 'marcas', 'unidades'));
    }

    //BUSCAR PRODUCTO POR CODIGO PARA VERIFICAR SI EXISTE CUANDO SE ESCANEA UN PRODUCTO EN LA VISTA INDEX DE PRODUCTOS
    public function buscar(Request $request): JsonResponse{

        $request->validate([
            'codigo' => 'required|string'
        ]);

        $codigo = $request->codigo;

        $producto = Producto::where('codigo', $codigo)->first();

        if ($producto) {
            return response()->json([
                'nombre' => $producto->nombre,
                'precio_venta' => $producto->precio_venta,
                'codigo' => $producto->codigo,
                'cantidad' => $producto->cantidad,
                'existe' => $producto !== null //para el filtrado de categorias si existe se filtran los productos  si no son null
            ]);
        }

        return response()->json([
            'error' => 'Producto no encontrado'
        ], 404);

    }


    //CAMBIAR ESTADO DE PRODUCTO DE ACTIVO
    public function cambiarEstado(Request $request, $id){
        $producto = Producto::findOrFail($id);
        $producto->activo = $request->activo;
        $producto->save();
        return response()->json(['message' => 'Estado Actualizado Correctamente']);
    }


    //FILTRAR LOS PRODUCTOS Y LAS CATEGORIAS y MARCAS
    public function filtrar(Request $request){

        // Iniciar la consulta base
        // Asegúrar de incluir las relaciones necesarias (imagen, categoria, etc.)
        //$query = Producto::with(['imagen', 'categoria', 'marca', 'moneda']);

       $query = Producto::query();

        // 1. Lógica del BUSCADOR (Nombre o Código)
        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;

            // Usamos un grupo (closure) para que el OR no rompa los otros filtros
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                ->orWhere('codigo', 'LIKE', "%{$busqueda}%"); // Asumiendo que tienes columna 'codigo'
            });
        }

        //Lógica de CATEGORÍAS (Aquí corregimos el error de "Todas")
        // Solo aplicamos el filtro si el ID NO es 'todas' y no está vacío
        if ($request->filled('categoria_id') && $request->categoria_id !== 'todas') {
            $query->where('categoria_id', $request->categoria_id);
        }

        // 3. Lógica de MARCAS (Igual que categorías)
        if ($request->filled('marca_id') && $request->marca_id !== 'todas') {
            $query->where('marca_id', $request->marca_id);
        }

        //Ordenamiento y Paginación Asegúrar de que los productos tengan cantidad > 0 en BD
        $productos = $query->where('cantidad', '>=', 0)->paginate(10);

        //Retornar vista parcial (JSON)
        if ($request->ajax()) {
            return response()->json([
                'html' => view('modulos.productos.listafiltrado', compact('productos'))->render(),
                'pagination' => (string) $productos->links()->render(),
                'total' => $productos->count(),
            ]);
        }

    }

    // Método adicional para búsqueda directa por código en la vista de nueva venta index para vender producto
    public function buscarPorCodigo(Request $request){
        $codigo = $request->input('codigo');

        $producto = Producto::where('cantidad', '>', 0)
                        ->where(function($q) use ($codigo) {
                            $q->where('codigo', $codigo);
                        })
                        ->first();

        if ($producto) {
            return response()->json([
                'success' => true,
                'producto' => [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'codigo' => $producto->codigo,
                    'precio' => $producto->precio_venta,
                    'stock' => $producto->cantidad,
                    'imagen' => $producto->imagen ? asset('storage/' . $producto->imagen->ruta) : null
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'producto' => null
        ]);
    }

    //IMPRIMIR ETIQUETAS DE PRODUCTOS DE CODIGO DE BARRAS
    public function imprimirEtiquetas(Request $request){

        $alcance = $request->input('alcance', 'todos');
        //Definir variables según el formato
        $formato = $request->input('formato', 'rollo_80mm');// Default

        //Filtrar productos según lo que eligió el usuario
        $query = Producto::query();

        // FILTROS
        switch ($alcance) {
            case 'oferta':
                // Filtra solo los que tienen oferta activa
                $query->where('en_oferta', 1)
                    ->whereNotNull('precio_oferta');
                break;

            case 'mayoreo':
                // Filtra solo los que permiten mayoreo
                $query->where('permite_mayoreo', 1)
                    ->whereNotNull('precio_mayoreo');
                break;

            case 'cantidad':
                $query->where('cantidad', '>', 0);
                break;
        }

        $productos = $query->get();

        return view('modulos.productos.etiquetas', compact('productos', 'formato', 'alcance'));
    }

    public function store(Request $request){

        // ==========================================
        // VALIDACIÓN COMPLETA
        // ==========================================
        $validated = $request->validate([
            // Relaciones requeridas
            'categoria_id' => 'required|exists:categorias,id',
            'unidad_id'    => 'required|exists:unidades,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'marca_id'     => 'required|exists:marcas,id',

            // Código de barras
            'codigo' => 'nullable|string|max:255|unique:productos,codigo',

            // Información básica
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',

            // Estado del producto (boolean)
            'activo' => 'required|boolean',

            // ========== CADUCIDAD ==========
            'requiere_fecha_caducidad' => 'boolean',
            'fecha_caducidad' => [
                'nullable',
                'date',
                'after:today',
                // Solo requerido si requiere_fecha_caducidad es true
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->boolean('requiere_fecha_caducidad') && empty($value)) {
                        $fail('La fecha de caducidad es obligatoria cuando el producto requiere control de vencimiento.');
                    }
                },
            ],

            // ========== MAYOREO ==========
            'permite_mayoreo' => 'boolean',
            'precio_mayoreo' => [
                'nullable',
                'numeric',
                'min:0',
                // Solo requerido si permite_mayoreo es true
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->boolean('permite_mayoreo') && (empty($value) || $value <= 0)) {
                        $fail('El precio de mayoreo debe ser mayor a 0 cuando está habilitado.');
                    }
                },
            ],
            'cantidad_minima_mayoreo' => [
                'nullable',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->boolean('permite_mayoreo') && (empty($value) || $value < 1)) {
                        $fail('La cantidad mínima de mayoreo debe ser al menos 1.');
                    }
                },
            ],

            // ========== OFERTAS ==========
            'en_oferta' => 'boolean',
            'precio_oferta' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->boolean('en_oferta') && (empty($value) || $value <= 0)) {
                        $fail('El precio de oferta debe ser mayor a 0 cuando está habilitada.');
                    }
                },
            ],
            'fecha_inicio_oferta' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->boolean('en_oferta') && empty($value)) {
                        $fail('La fecha de inicio de oferta es obligatoria.');
                    }
                },
            ],
            'fecha_fin_oferta' => [
                'nullable',
                'date',
                'after_or_equal:fecha_inicio_oferta',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->boolean('en_oferta') && empty($value)) {
                        $fail('La fecha de fin de oferta es obligatoria.');
                    }
                },
            ],

            // Imagen
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            // Precios (configurados en compras)
            'precio_compra' => 'nullable|numeric|min:0',
            'cantidad'      => 'nullable|integer|min:0',
        ], [
            // Mensajes personalizados
            'categoria_id.required' => 'La categoría es obligatoria.',
            'categoria_id.exists'   => 'La categoría seleccionada no es válida.',

            'unidad_id.required' => 'La unidad es obligatoria.',
            'unidad_id.exists'   => 'La unidad seleccionada no es válida.',

            'proveedor_id.required' => 'El proveedor es obligatorio.',
            'proveedor_id.exists'   => 'El proveedor seleccionado no es válido.',

            'marca_id.required' => 'La marca es obligatoria.',
            'marca_id.exists'   => 'La marca seleccionada no es válida.',

            'codigo.unique' => 'Este código de barras ya está registrado.',
            'codigo.max'    => 'El código no debe exceder 255 caracteres.',

            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max'      => 'El nombre no debe exceder 255 caracteres.',

            'descripcion.max' => 'La descripción no debe exceder 500 caracteres.',

            'activo.required' => 'Debe especificar el estado del producto.',
            'activo.boolean'  => 'El estado debe ser verdadero o falso.',

            'fecha_caducidad.after' => 'La fecha de caducidad debe ser posterior a hoy.',

            'precio_mayoreo.numeric' => 'El precio de mayoreo debe ser numérico.',
            'precio_mayoreo.min'     => 'El precio de mayoreo no puede ser negativo.',

            'cantidad_minima_mayoreo.integer' => 'La cantidad debe ser un número entero.',
            'cantidad_minima_mayoreo.min'     => 'La cantidad mínima debe ser al menos 1.',

            'precio_oferta.numeric' => 'El precio de oferta debe ser numérico.',
            'precio_oferta.min'     => 'El precio de oferta no puede ser negativo.',

            'fecha_fin_oferta.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',

            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'imagen.max'   => 'La imagen no debe superar los 2MB.',
        ]);

        DB::beginTransaction();

        try{

            // generacion o validacion de codigo
            $codigo = $this->procesarCodigoBarras($validated['codigo'] ?? null);

            //Crear producto con todos los campos
            $datosProducto = [
                'user_id'      => Auth::id(),
                'categoria_id' => $validated['categoria_id'],
                'unidad_id'    => $validated['unidad_id'],
                'proveedor_id' => $validated['proveedor_id'],
                'marca_id'     => $validated['marca_id'],
                'codigo'       => $codigo,
                'nombre'       => $validated['nombre'],
                'descripcion'  => $validated['descripcion'] ?? null,

                // Estado del producto (convertir a boolean)
                'activo' => $request->boolean('activo'),

                // Caducidad
                'requiere_fecha_caducidad' => $request->boolean('requiere_fecha_caducidad'),
                'fecha_caducidad' => $request->boolean('requiere_fecha_caducidad')
                    ? ($validated['fecha_caducidad'] ?? null)
                    : null,

                // Mayoreo
                'permite_mayoreo' => $request->boolean('permite_mayoreo'),
                'precio_mayoreo' => $request->boolean('permite_mayoreo')
                    ? ($validated['precio_mayoreo'] ?? 0)
                    : 0,
                'cantidad_minima_mayoreo' => $request->boolean('permite_mayoreo')
                    ? ($validated['cantidad_minima_mayoreo'] ?? 0)
                    : 0,

                // Ofertas
                'en_oferta' => $request->boolean('en_oferta'),
                'precio_oferta' => $request->boolean('en_oferta')
                    ? ($validated['precio_oferta'] ?? 0)
                    : 0,
                'fecha_inicio_oferta' => $request->boolean('en_oferta')
                    ? ($validated['fecha_inicio_oferta'] ?? null)
                    : null,
                'fecha_fin_oferta' => $request->boolean('en_oferta')
                    ? ($validated['fecha_fin_oferta'] ?? null)
                    : null,

                // Precios y stock (se configuran después en compras)
                'precio_compra' => $validated['precio_compra'] ?? 0,
                'cantidad'      => $validated['cantidad'] ?? 0,
            ];

            // Generar código de barras (imagen)
            if ($codigo) {
                $datosProducto['barcode_path'] = $this->generarCodigoBarras($codigo);
            }

            // crear producto
            $producto = Producto::create($datosProducto);

            // Verificar que el producto se creó correctamente
            if (!$producto) {
                throw new Exception('No se pudo crear el producto en la base de datos.');
            }

            // Si hay imagen o contiene, subirla
            if($request->hasFile('imagen')){
                try {
                    $this->subir_imagen($request, $producto->id);
                } catch (Exception $imageError) {
                    Log::error('Error al subir imagen del producto: ' . $imageError->getMessage());
                    // Continuar aunque falle la imagen (no crítico)
                }
            }

            DB::commit();

            // Devolver los datos del producto creado para cuando se crea un producto con modal Respuesta AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producto creado exitosamente.',
                    'producto' => [
                        'id'          => $producto->id,
                        'codigo'      => $producto->codigo,
                        'nombre'      => $producto->nombre,
                        'descripcion' => $producto->descripcion,
                        'activo'      => $producto->activo,

                        // Relaciones
                        'categoria'   => $producto->categoria->nombre ?? '',
                        'unidad'      => $producto->unidad->nombre ?? '',
                        'marca'       => $producto->marca->nombre ?? '',
                        'proveedor'   => $producto->proveedor->nombre ?? '',

                        // Caducidad
                        'requiere_fecha_caducidad' => $producto->requiere_fecha_caducidad,
                        'fecha_caducidad' => $producto->fecha_caducidad
                            ? $producto->fecha_caducidad->format('d/m/Y')
                            : null,

                        // Mayoreo
                        'permite_mayoreo' => $producto->permite_mayoreo,
                        'precio_mayoreo'  => $producto->precio_mayoreo,
                        'cantidad_minima_mayoreo' => $producto->cantidad_minima_mayoreo,

                        // Ofertas
                        'en_oferta'     => $producto->en_oferta,
                        'precio_oferta' => $producto->precio_oferta,
                        'fecha_inicio_oferta' => $producto->fecha_inicio_oferta
                            ? $producto->fecha_inicio_oferta->format('d/m/Y')
                            : null,
                        'fecha_fin_oferta' => $producto->fecha_fin_oferta
                            ? $producto->fecha_fin_oferta->format('d/m/Y')
                            : null,

                        // Imagen
                        'imagen' => $producto->imagen
                            ? asset('storage/' . $producto->imagen->ruta)
                            : null,

                        'created_at' => $producto->created_at->format('d/m/Y H:i'),
                    ]
                ]);
            }

            return redirect()
                ->route('producto.index')
                ->with('success', 'Producto creado exitosamente. Puedes realizar la compra más tarde usando el botón Comprar.!');

        }catch (ValidationException $e){
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación.',
                    'errors'  => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();

        }catch (Exception $e){
            DB::rollBack();
            Log::error('Error al crear producto: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el producto: ' . $e->getMessage(),
                    'error_details' => config('app.debug') ? $e->getTraceAsString() : null
                ], 500);
            }

            return back()
                ->withErrors(['error' => 'Error al crear el producto. Por favor, intenta nuevamente.'])
                ->withInput();

        }
    }

    //Procesar y validar código de barras
    private function procesarCodigoBarras(?string $codigoInput): string {
        // Si se proporciona un código
        if (!empty($codigoInput)) {
            $codigo = trim($codigoInput);

            // Validar formato numérico de 13 dígitos para EAN-13
            if (!preg_match('/^\d{13}$/', $codigo)) {
                throw ValidationException::withMessages([
                    'codigo' => 'El código debe ser numérico de 13 dígitos (EAN-13).'
                ]);
            }

            // Validar dígito verificador
            if (!$this->validateEAN13($codigo)) {
                throw ValidationException::withMessages([
                    'codigo' => 'El dígito verificador del código EAN-13 es incorrecto.'
                ]);
            }

            return $codigo;
        }

        // Generar código automáticamente
        return $this->generarCodigoEAN13();
    }

    //Generar código EAN-13 único
    private function generarCodigoEAN13(): string {
        $maxIntentos = 10;
        $intento = 0;

        do {
            // Prefijo 200 para uso interno + 9 dígitos aleatorios
            $base12 = '200' . str_pad((string)mt_rand(0, 999999999), 9, '0', STR_PAD_LEFT);

            // Calcular dígito verificador
            $digitoVerificador = $this->calcularDigitoVerificador($base12);

            // Código completo
            $codigo = $base12 . $digitoVerificador;

            $intento++;

            // Verificar que no exista en la BD
            if (!Producto::where('codigo', $codigo)->exists()) {
                return $codigo;
            }

        } while ($intento < $maxIntentos);

        throw new Exception('No se pudo generar un código de barras único. Intenta nuevamente.');
    }

    // Función auxiliar para calcular dígito (necesaria para el generador interno)
    private function calcularDigitoVerificador(string $digits): int {
        // Validar longitud
        $length = strlen($digits);
        if ($length !== 12) {
            throw new Exception("Se requieren exactamente 12 dígitos, se recibieron {$length}");
        }

        //Validar que sean dígitos
        if (!ctype_digit($digits)) {
            throw new Exception("El código base debe contener solo dígitos numéricos");
        }

        // Calcular suma
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int)$digits[$i];

            //Código más legible
            $multiplier = ($i % 2 === 0) ? 1 : 3;
            $sum += $digit * $multiplier;
        }

        // Calcular dígito verificador
        $remainder = $sum % 10;
        $checkDigit = ($remainder === 0) ? 0 : (10 - $remainder);

        //Log de debug
        Log::debug("Dígito verificador calculado", [
            'codigo_base' => $digits,
            'suma' => $sum,
            'digito_verificador' => $checkDigit
        ]);

        return $checkDigit;
    }

    public function productCodeExists($number){
        return Producto::whereProductCode($number)->exists();
    }


    //Validar código EAN-13
    private function validateEAN13(string $ean13): bool {
        // Type hints
        if (strlen($ean13) !== 13 || !ctype_digit($ean13)) {
            // Log de debug
            Log::debug("EAN-13 inválido (formato incorrecto)", ['codigo' => $ean13]);
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int)$ean13[$i];

            //Comentarios más claros
            $multiplier = ($i % 2 === 0) ? 1 : 3;
            $sum += $digit * $multiplier;
        }

        //Variables explícitas
        $remainder = $sum % 10;
        $checkDigitExpected = ($remainder === 0) ? 0 : (10 - $remainder);
        $checkDigitProvided = (int)$ean13[12];

        // Comparación estricta
        $isValid = ($checkDigitExpected === $checkDigitProvided);

        //Log cuando falla
        if (!$isValid) {
            Log::debug("EAN-13 inválido (dígito verificador incorrecto)", [
                'codigo' => $ean13,
                'esperado' => $checkDigitExpected,
                'recibido' => $checkDigitProvided
            ]);
        }

        return $isValid;
    }

    public function subir_imagen(Request $request, int $productoId): bool {
        if (!$request->hasFile('imagen')) {
            Log::info("No se proporcionó imagen para el producto ID: {$productoId}");
            return false;
        }

        $file = $request->file('imagen');

        // Validar que el archivo sea válido
        if (!$file->isValid()) {
            Log::error("Archivo de imagen inválido para producto ID: {$productoId}");
            throw new Exception('El archivo de imagen no es válido o está corrupto.');
        }

        try {
            // Usar transacciones
            DB::beginTransaction();

            // Eliminar imagen anterior
            $imagenExistente = Imagen::where('producto_id', $productoId)->first();

            if ($imagenExistente) {
                //Verificar existencia antes de eliminar
                if (Storage::disk('public')->exists($imagenExistente->ruta)) {
                    $eliminado = Storage::disk('public')->delete($imagenExistente->ruta);

                    if (!$eliminado) {
                        Log::warning("No se pudo eliminar la imagen anterior", [
                            'ruta' => $imagenExistente->ruta,
                            'producto_id' => $productoId
                        ]);
                    }
                }

                $imagenExistente->delete();

                Log::info("Imagen anterior eliminada", [
                    'producto_id' => $productoId,
                    'ruta_eliminada' => $imagenExistente->ruta
                ]);
            }

            //Generar nombre único con timestamp
            $extension = $file->getClientOriginalExtension();
            $nombreOriginal = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            // Sanitizar nombre (remover caracteres especiales)
            $nombreSanitizado = preg_replace('/[^A-Za-z0-9\-_]/', '', $nombreOriginal);
            $nombreSanitizado = substr($nombreSanitizado, 0, 50);

            $nombreUnico = 'producto_' . $productoId . '_' . time() . '_' . $nombreSanitizado . '.' . $extension;

            //Usar storeAs para control total
            $rutaRelativa = $file->storeAs(
                'imagenes/productos',
                $nombreUnico,
                'public'
            );

            if (!$rutaRelativa) {
                throw new Exception('No se pudo guardar el archivo de imagen en el storage.');
            }

            // Verificar que el archivo se guardó
            if (!Storage::disk('public')->exists($rutaRelativa)) {
                throw new Exception('El archivo de imagen no existe después de guardarlo.');
            }

            $tamañoArchivo = Storage::disk('public')->size($rutaRelativa);

            //Guardar metadatos adicionales
            $imagen = Imagen::create([
                'producto_id' => $productoId,
                'nombre' => $nombreUnico,
                'ruta' => $rutaRelativa,
                /* 'tamaño' => $tamañoArchivo,
                'mime_type' => $file->getMimeType(), */
            ]);

            if (!$imagen) {
                throw new Exception('No se pudo crear el registro de imagen en la base de datos.');
            }

            //Commit de la transacción
            DB::commit();

            //Logs detallados
            Log::info("Imagen subida exitosamente", [
                'producto_id' => $productoId,
                'imagen_id' => $imagen->id,
                'ruta' => $rutaRelativa,
                'tamaño' => $this->formatBytes($tamañoArchivo)
            ]);

            return true;

        } catch (Exception $e) {
            // Rollback en caso de error
            DB::rollBack();

            Log::error("Error al subir imagen", [
                'producto_id' => $productoId,
                'error' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile()
            ]);

            throw $e;
        }

    }

    /**
     * metodo opcional Elimina una imagen del producto
     * @param int $productoId - ID del producto
     * @return bool - true si se eliminó, false si no había imagen
     */
    public function eliminarImagen(int $productoId): bool {
        try {
            $imagen = Imagen::where('producto_id', $productoId)->first();

            if (!$imagen) {
                Log::info("No hay imagen para eliminar del producto ID: {$productoId}");
                return false;
            }

            DB::beginTransaction();

            // Eliminar archivo físico
            if (Storage::disk('public')->exists($imagen->ruta)) {
                Storage::disk('public')->delete($imagen->ruta);
            }

            // Eliminar registro
            $imagen->delete();

            DB::commit();

            Log::info("Imagen eliminada exitosamente", [
                'producto_id' => $productoId,
                'ruta' => $imagen->ruta
            ]);

            return true;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error("Error al eliminar imagen", [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    public function update(Request $request, Producto $producto){

        //Determinar si el codigo es editable
        $codigoEsEditable = $producto->codigoEsEditable();
        $codigoCambio = false;

        // validacion dinamica segun editabilidad
        $rules = [
            // Relaciones requeridas
            'categoria_id' => 'required|exists:categorias,id',
            'unidad_id'    => 'required|exists:unidades,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'marca_id'     => 'required|exists:marcas,id',

            // Información básica
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string|max:500',

            // Precios
            'precio_venta' => 'required|numeric|min:0',

            // Estado (boolean)
            'activo' => 'required|boolean',

            // ========== CADUCIDAD ==========
            'requiere_fecha_caducidad' => 'boolean',
            'fecha_caducidad' => [
                'nullable',
                'date',
                'after:today',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->boolean('requiere_fecha_caducidad') && empty($value)) {
                        $fail('La fecha de caducidad es obligatoria cuando está habilitada.');
                    }
                },
            ],

            // ========== MAYOREO ==========
            'permite_mayoreo' => 'boolean',
            'precio_mayoreo' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->boolean('permite_mayoreo') && (empty($value) || $value <= 0)) {
                        $fail('El precio de mayoreo debe ser mayor a 0 cuando está habilitado.');
                    }
                },
            ],
            'cantidad_minima_mayoreo' => [
                'nullable',
                'integer',
                'min:0',// Permite 0 cuando mayoreo está desactivado
                function ($attribute, $value, $fail) use ($request) {
                    // Solo valida >= 1 si mayoreo está ACTIVO
                    if ($request->boolean('permite_mayoreo') && (empty($value) || $value < 1)) {
                        $fail('La cantidad mínima debe ser al menos 1 cuando el mayoreo está habilitado.');
                    }
                },
            ],

            // ========== OFERTAS ==========
            'en_oferta' => 'boolean',
            'precio_oferta' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->boolean('en_oferta') && (empty($value) || $value <= 0)) {
                        $fail('El precio de oferta debe ser mayor a 0 cuando está habilitada.');
                    }
                },
            ],
            'fecha_inicio_oferta' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->boolean('en_oferta') && empty($value)) {
                        $fail('La fecha de inicio es obligatoria cuando la oferta está habilitada.');
                    }
                },
            ],
            'fecha_fin_oferta' => [
                'nullable',
                'date',
                'after_or_equal:fecha_inicio_oferta',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->boolean('en_oferta') && empty($value)) {
                        $fail('La fecha de fin es obligatoria cuando la oferta está habilitada.');
                    }
                },
            ],

            // Imagen
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];

        // Agregar validación del código solo si es editable
        if ($codigoEsEditable) {
            $rules['codigo'] = [
                'required',
                'string',
                'max:255',
                'unique:productos,codigo,' . $producto->id,
                function ($attribute, $value, $fail) {
                    // Validar formato EAN-13
                    if (strlen($value) !== 13 || !ctype_digit($value)) {
                        $fail('El código debe ser numérico de 13 dígitos.');
                    }

                    // Validar dígito verificador
                    if (!$this->validateEAN13($value)) {
                        $fail('El dígito verificador del código es incorrecto.');
                    }
                },
            ];
        }

        // Mensajes personalizados
        $messages = [
            'categoria_id.required' => 'La categoría es obligatoria.',
            'categoria_id.exists'   => 'La categoría seleccionada no es válida.',

            'unidad_id.required' => 'La unidad es obligatoria.',
            'unidad_id.exists'   => 'La unidad seleccionada no es válida.',

            'proveedor_id.required' => 'El proveedor es obligatorio.',
            'proveedor_id.exists'   => 'El proveedor seleccionado no es válido.',

            'marca_id.required' => 'La marca es obligatoria.',
            'marca_id.exists'   => 'La marca seleccionada no es válida.',

            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max'      => 'El nombre no debe exceder 255 caracteres.',

            'descripcion.max' => 'La descripción no debe exceder 500 caracteres.',

            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.numeric'  => 'El precio de venta debe ser numérico.',
            'precio_venta.min'      => 'El precio de venta no puede ser negativo.',

            'activo.required' => 'Debe especificar el estado del producto.',
            'activo.boolean'  => 'El estado debe ser verdadero o falso.',

            'fecha_fin_oferta.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',

            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'imagen.max'   => 'La imagen no debe superar los 2MB.',
        ];

        //validar request
        try {
            $validated = $request->validate($rules, $messages);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación.',
                    'errors'  => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // verificar intento de cambio de codigo no permitido
        if (!$codigoEsEditable && $request->has('codigo') && $request->codigo !== $producto->codigo) {
            $error = 'No puedes cambiar el código de barras de un producto con ventas registradas.';

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $error,
                    'errors' => ['codigo' => [$error]]
                ], 422);
            }

            return back()
                ->withErrors(['codigo' => $error])
                ->withInput();
        }

        DB::beginTransaction();

        try{

            // detectar si el codigo cambio
            if ($codigoEsEditable && isset($validated['codigo']) && $validated['codigo'] !== $producto->codigo) {
                $codigoCambio = true;

                Log::info('Cambio de código de barras detectado', [
                    'producto_id' => $producto->id,
                    'codigo_anterior' => $producto->codigo,
                    'codigo_nuevo' => $validated['codigo']
                ]);
            }

            // verificar vigencia de oferta
            $enOferta = $request->boolean('en_oferta');
            $fechaFin = $validated['fecha_fin_oferta'] ?? null;

            if ($enOferta && $fechaFin) {
                if (\Carbon\Carbon::parse($fechaFin)->isPast()) {// Si la fecha fin ya pasó, desactivar oferta
                    $enOferta = false;
                    Log::info('Oferta desactivada automáticamente (fecha vencida)', [
                        'producto_id' => $producto->id,
                        'fecha_fin' => $fechaFin
                    ]);
                }
            }

            // procesar fecha de caducidad
            $requiereCaducidad = $request->boolean('requiere_fecha_caducidad');
            $fechaCaducidad = $validated['fecha_caducidad'] ?? null;

            // Si se desactiva el switch checkbox de caducidad, limpiar la fecha
            if (!$requiereCaducidad) {
                $fechaCaducidad = null;
            }

            // Preparar datos para actualizar
            $updateData = [
                'user_id'      => Auth::id(),
                'categoria_id' => $validated['categoria_id'],
                'unidad_id'    => $validated['unidad_id'],
                'proveedor_id' => $validated['proveedor_id'],
                'marca_id'     => $validated['marca_id'],
                'nombre'       => $validated['nombre'],
                'descripcion'  => $validated['descripcion'] ?? null,

                // Estado
                'activo' => $request->boolean('activo'),

                // Precios
                'precio_venta' => $validated['precio_venta'],

                // Mayoreo (limpiar si no está habilitado)
                'permite_mayoreo' => $request->boolean('permite_mayoreo'),
                'precio_mayoreo' => $request->boolean('permite_mayoreo')
                    ? ($validated['precio_mayoreo'] ?? 0)
                    : 0,
                'cantidad_minima_mayoreo' => $request->boolean('permite_mayoreo')
                    ? ($validated['cantidad_minima_mayoreo'] ?? 0)
                    : 0,

                // Ofertas (limpiar si no está habilitado)
                'en_oferta' => $enOferta,
                'precio_oferta' => $enOferta
                    ? ($validated['precio_oferta'] ?? 0)
                    : 0,
                'fecha_inicio_oferta' => $enOferta
                    ? ($validated['fecha_inicio_oferta'] ?? null)
                    : null,
                'fecha_fin_oferta' => $enOferta
                    ? $fechaFin
                    : null,

                // Caducidad
                'requiere_fecha_caducidad' => $requiereCaducidad,
                'fecha_caducidad' => $fechaCaducidad,
            ];

            // Incluir código solo si es editable
            if ($codigoEsEditable && isset($validated['codigo'])) {
                $updateData['codigo'] = $validated['codigo'];
            }

            // Actualizar producto
            $producto->update($updateData);

            // regenerar codigo de barras si cambio
            if ($codigoCambio) {
                try {
                    $this->regenerarCodigoBarras($producto);
                } catch (Exception $barcodeError) {
                    Log::error('Error al regenerar código de barras', [
                        'producto_id' => $producto->id,
                        'error' => $barcodeError->getMessage()
                    ]);
                    // No detener el proceso, solo log
                }
            }

            // manejar eliminacion de imagen
            if ($request->has('eliminar_imagen') && $request->eliminar_imagen == '1') {
                try {
                    $this->eliminarImagen($producto->id);
                } catch (Exception $deleteError) {
                    Log::warning('No se pudo eliminar imagen', [
                        'producto_id' => $producto->id,
                        'error' => $deleteError->getMessage()
                    ]);
                }
            }

            // subir nueva imagen si existe
            if ($request->hasFile('imagen')) {
                try {
                    $this->subir_imagen($request, $producto->id);
                } catch (Exception $imageError) {
                    Log::warning('Error al subir nueva imagen', [
                        'producto_id' => $producto->id,
                        'error' => $imageError->getMessage()
                    ]);
                    // Continuar aunque falle la imagen
                }
            }

            DB::commit();

            // preparar mensaje de exito
            $mensaje = $codigoCambio
                ? 'Producto actualizado exitosamente. Se ha generado un nuevo código de barras.'
                : 'Producto actualizado exitosamente.';


            //respuesta segun tipo de request AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $mensaje,
                    'data' => [
                        'producto' => $producto->load(['categoria', 'unidad', 'proveedor', 'marca', 'imagen']),
                        'codigo_cambio' => $codigoCambio
                    ]
                ]);
            }

            return redirect()->route('producto.index')->with('success', $mensaje);

        }catch(Exception $e){
            DB::rollBack();

            Log::error('Error al actualizar producto', [
                'producto_id' => $producto->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el producto: ' . $e->getMessage(),
                    'error_details' => config('app.debug') ? $e->getTraceAsString() : null
                ], 500);
            }


            return back()
                ->withErrors(['error' => 'Error al actualizar el producto.'])
                ->withInput();

        }
    }

    //Formatear tamaño de archivos
    private function formatBytes(int $bytes, int $precision = 2): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    //Desactivar Productos Vencidos
    public function desactivar($id){
        try {
            $producto = Producto::findOrFail($id);

            // Verificar que esté vencido
            if (!$producto->estaVencido()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El producto no está vencido.'
                ], 400);
            }

            $producto->update(['activo' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Producto desactivado exitosamente.'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al desactivar el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /* Genera un código de barras para un código dado
    @param string $codigo - El código para generar el barcode
    @return string|null - Retorna la ruta del barcode o null si falla */
    private function generarCodigoBarras(string $codigo): ?string {
        try {
            // Validación inicial del código
            if (strlen($codigo) !== 13 || !ctype_digit($codigo)) {
                Log::warning("Código inválido para generar barcode: {$codigo}");
                return null;
            }

            // Configuración de tamaño y altura
            $barcodeBase64 = DNS1D::getBarcodePNG($codigo, 'EAN13', 2, 60);

            // Validar que DNS1D generó algo
            if (empty($barcodeBase64)) {
                throw new Exception('DNS1D no pudo generar el código de barras');
            }

            // Definir rutas
            $barcodeFilename = $codigo . '.png';
            $barcodeRelativePath = 'barcodes/' . $barcodeFilename;
            $barcodeFullPath = public_path($barcodeRelativePath);

            //Usar is_dir() en lugar de file_exists()
            $barcodeDir = public_path('barcodes');
            if (!is_dir($barcodeDir)) {
                if (!mkdir($barcodeDir, 0755, true)) {
                    throw new Exception('No se pudo crear el directorio de barcodes');
                }
            }

            // Decodificar y guardar
            $barcodeDecoded = base64_decode($barcodeBase64);

            // Validar decodificación
            if ($barcodeDecoded === false) {
                throw new Exception('No se pudo decodificar el código de barras base64');
            }

            $bytesWritten = file_put_contents($barcodeFullPath, $barcodeDecoded);

            //Validar escritura
            if ($bytesWritten === false || $bytesWritten === 0) {
                throw new Exception('No se pudo escribir el archivo del código de barras');
            }

            // Verificar existencia del archivo
            if (!file_exists($barcodeFullPath) || filesize($barcodeFullPath) === 0) {
                throw new Exception('El archivo del código de barras está vacío o no existe');
            }

            //Logs estructurados con contexto
            Log::info("Código de barras generado exitosamente", [
                'codigo' => $codigo,
                'ruta' => $barcodeRelativePath,
                'tamaño' => $bytesWritten . ' bytes'
            ]);

            return $barcodeRelativePath;

        } catch (Exception $e) {
            //Logs detallados de error
            Log::error("Error al generar código de barras", [
                'codigo' => $codigo,
                'error' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile()
            ]);

            return null;
        }
    }


    //Regenera el código de barras para un producto si se cambia y no tiene ventas asociada
    private function regenerarCodigoBarras(Producto $producto): void {
        try {
            // eliminar codigo de barras anterior
            if ($producto->barcode_path) {
                $rutaAnterior = public_path($producto->barcode_path);

                if (file_exists($rutaAnterior)) {
                    $eliminado = unlink($rutaAnterior);

                    if ($eliminado) {
                        Log::info('Código de barras anterior eliminado', [
                            'producto_id' => $producto->id,
                            'ruta' => $producto->barcode_path
                        ]);
                    } else {
                        Log::warning('No se pudo eliminar código de barras anterior', [
                            'producto_id' => $producto->id,
                            'ruta' => $producto->barcode_path
                        ]);
                    }
                }
            }

            // generar nuevo codigo de barras
            $nuevaRuta = $this->generarCodigoBarras($producto->codigo);

            // actualizar ruta en la bd
            if ($nuevaRuta) {
                $producto->update(['barcode_path' => $nuevaRuta]);

                Log::info('Código de barras regenerado exitosamente', [
                    'producto_id' => $producto->id,
                    'codigo' => $producto->codigo,
                    'nueva_ruta' => $nuevaRuta
                ]);
            } else {
                // Si falla, establecer como null
                $producto->update(['barcode_path' => null]);

                Log::warning('No se pudo generar nuevo código de barras', [
                    'producto_id' => $producto->id,
                    'codigo' => $producto->codigo
                ]);
            }

        } catch (Exception $e) {
            Log::error('Error al regenerar código de barras', [
                'producto_id' => $producto->id,
                'codigo' => $producto->codigo,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Establecer barcode_path como null en caso de error
            try {
                $producto->update(['barcode_path' => null]);
            } catch (Exception $updateError) {
                Log::error('Error al actualizar barcode_path a null', [
                    'producto_id' => $producto->id,
                    'error' => $updateError->getMessage()
                ]);
            }

            // Re-lanzar excepción para que el llamador pueda manejarla
            throw $e;
        }
    }

    // Para manejar el modal de eliminar
    public function deleteModal($id){
        $producto = Producto::with(['categoria', 'proveedor', 'marca', 'imagen'])
            ->findOrFail($id);

        return view('modulos.productos.partials.delete-modal', compact('producto'));
    }

    public function destroy(Producto $producto, Request $request){

        DB::beginTransaction();

        try {
            $nombreProducto = $producto->nombre;

            if ($producto->tieneCompras()) {
                // Marcar como inactivo
                $producto->activo = false;
                $producto->save();

                DB::commit();

                $mensaje = "El producto '{$nombreProducto}' fue marcado como inactivo porque tiene compras registradas.";

                return $request->ajax()
                    ? response()->json(['success' => true, 'message' => $mensaje])
                    : redirect()->route('producto.index')->with('info', $mensaje);
            }

            // Eliminar normal
            if ($producto->imagen && $producto->imagen->ruta) {
                if (Storage::disk('public')->exists($producto->imagen->ruta)) {
                    Storage::disk('public')->delete($producto->imagen->ruta);
                }
                $producto->imagen->delete();
            }

            $producto->delete();

            DB::commit();

            $mensaje = "Producto '{$nombreProducto}' eliminado correctamente.";

            return $request->ajax()
                ? response()->json(['success' => true, 'message' => $mensaje])
                : redirect()->route('producto.index')->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar producto: ' . $e->getMessage());

            return $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Ocurrió un error al eliminar el producto.'], 500)
                : redirect()->route('producto.index')->with('error', 'Ocurrió un error al eliminar el producto.');
        }
    }

    //IMPORTANTE: para importar productos masivamente excel
    public function importar(Request $request){

        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,csv,xls'
        ]);

        try {
            Excel::import(new ProductosImport, $request->file('archivo_excel'));

            return back()->with('success', '¡Productos importados correctamente!');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $fallas = $e->failures();
            $mensaje = "Error en la importación: ";
            foreach ($fallas as $falla) {
                $mensaje .= "Fila " . $falla->row() . ": " . implode(', ', $falla->errors()) . ". ";
            }
            return back()->with('error', $mensaje);
        } catch (\Exception $e) {
            return back()->with('error', 'Error general: ' . $e->getMessage());
        }
    }

    //metodo para manejar la creación rápida de Categorías, Marcas y Proveedores dinámicamente en el modal de crear nuevo producto .
    public function quickStore(Request $request, $type){
        // Validar que se envíe un nombre
        $request->validate(['nombre' => 'required|string|max:255']);

        $newItem = null;

        try {
            // Switch para saber qué estamos creando
            switch ($type) {
                case 'categoria':
                    $newItem = Categoria::create([
                        'nombre' => $request->nombre,
                        'descripcion' => 'Creada desde creación rápida',
                        'user_id' => auth()->id(), // Si tu tabla lo requiere
                        'medida' => 'Pieza'
                    ]);
                    break;

                case 'marca':
                    $newItem = Marca::create([
                        'nombre' => $request->nombre,
                        'user_id' => auth()->id()
                    ]);
                    break;

                case 'proveedor':
                    $newItem = Proveedor::create([
                        'nombre' => $request->nombre,
                        'user_id' => auth()->id(),
                        'email' => 'Example@hotmail.com',
                        'codigo_postal' => '24040',
                        'telefono' => '0000000000',
                        'direccion' => 'Sin dirección'
                    ]);
                    break;

                default:
                    return response()->json(['message' => 'Tipo no válido'], 400);
            }

            return response()->json([
                'success' => true,
                'id' => $newItem->id,
                'nombre' => $newItem->nombre
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

}
