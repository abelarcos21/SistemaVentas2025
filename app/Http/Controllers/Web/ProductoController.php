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
            'proveedores.nombre as nombre_proveedor',
            'marcas.nombre as nombre_marca',
            'imagens.ruta as imagen_producto',
            'imagens.id as imagen_id'
        )
        ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
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

        return view('modulos.productos.partials.create-modal', compact('categorias', 'proveedores', 'marcas'));
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

        return view('modulos.productos.partials.edit-modal', compact('producto', 'categorias', 'proveedores', 'marcas'));
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

        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'marca_id' => 'required|exists:marcas,id',
            'codigo' => 'nullable|string|max:255|unique:productos,codigo',

            // Campos de precios y promociones
            'precio_compra' => 'nullable|numeric|min:0',
            'cantidad' => 'nullable|integer|min:0',

            'permite_mayoreo' => 'boolean',
            'en_oferta' => 'boolean',
            'precio_mayoreo' => 'nullable|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0',
            'cantidad_minima_mayoreo' => 'nullable|integer|min:1',
            'fecha_inicio_oferta' => 'nullable|date',
            'fecha_fin_oferta' => 'nullable|date|after_or_equal:fecha_inicio_oferta',

            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'activo' => 'required|boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            // CAMPOS DE CADUCIDAD
            'requiere_fecha_caducidad' => 'boolean',
            'fecha_caducidad' => 'nullable|required_if:requiere_fecha_caducidad,1|date|after:today',
        ], [
            'fecha_caducidad.required_if' => 'La fecha de caducidad es obligatoria cuando se activa el control de caducidad.',
            'fecha_caducidad.after' => 'La fecha de caducidad debe ser posterior a hoy.',
        ]);


        DB::beginTransaction();

        try{

            $codigo = null;

            // CASO A: SI TIENES EL PRODUCTO EN MANO (Escáner)
            if(!empty($validated['codigo'])){
                $codigo = $validated['codigo'];

            // Validación estricta solo si es numérico (para evitar errores de dedo)
            if (!preg_match('/^\d{13}$/', $codigo)) {
                 // Opcional: Si tus productos tienen códigos cortos (UPC de 12 o EAN-8), ajusta el regex
                throw ValidationException::withMessages(['codigo' => 'El código debe ser numérico de 13 dígitos.']);
            }

            // Validar checksum solo si es un código estándar EAN
            if (!$this->validateEAN13($codigo)) {
                throw ValidationException::withMessages(['codigo' => 'Dígito verificador incorrecto.']);
            }

            }else{

                // CASO B: PRODUCTO A GRANEL O SIN CÓDIGO (Generación Interna)
                // CAMBIO IMPORTANTE: Usamos prefijo '200' para uso interno, NO '750'.
                do{

                    // Generamos 12 dígitos: prefijo 200 + 9 aleatorios
                    $base12 = '200' . str_pad(mt_rand(0, 999999999), 9, '0', STR_PAD_LEFT);

                    // Calculamos el dígito verificador para que el escáner lo lea bien
                    $codigo = $base12 . $this->calcularDigitoVerificador($base12);

                } while (Producto::where('codigo', $codigo)->exists());
            }

            // Generar ruta de imagen de barras (Solo si la necesitas guardar como archivo)
            // Si usas una fuente de código de barras en el frontend, esto no es necesario guardarlo en BD.
            $barcodePath = $this->generarCodigoBarras($codigo);

            //Crear producto con todos los campos
            $producto = Producto::create([
                'user_id' => Auth::id(),
                'categoria_id' => $validated['categoria_id'],
                'proveedor_id' => $validated['proveedor_id'],
                'marca_id'     => $validated['marca_id'],
                'codigo'       => $codigo,
                'barcode_path' => $barcodePath,
                'nombre'       => $validated['nombre'],
                'descripcion'  => $validated['descripcion'],
                'activo'       => $validated['activo'],

                // 🔹 Campos de precios y stock los creo en la compra y en el editar del producto
            /*  'precio_venta' => $validated['precio_venta'], */
                /* 'precio_compra' => $validated['precio_compra'] ?? 0, */
            /*  'cantidad' => $validated['cantidad'] ?? 0, */

                // 🔹 Mayoreo
                'permite_mayoreo' => $request->boolean('permite_mayoreo'),
                'precio_mayoreo' => $validated['precio_mayoreo'] ?? 0,
                'cantidad_minima_mayoreo' => $validated['cantidad_minima_mayoreo'] ?? 0,

                // 🔹 Oferta
                'en_oferta' => $request->boolean('en_oferta'),
                'precio_oferta' => $validated['precio_oferta'] ?? 0,
                'fecha_inicio_oferta' => $validated['fecha_inicio_oferta'] ?? null,
                'fecha_fin_oferta' => $validated['fecha_fin_oferta'] ?? null,

                // 🔹 CADUCIDAD
                'requiere_fecha_caducidad' => $request->boolean('requiere_fecha_caducidad'),
                'fecha_caducidad' => $validated['fecha_caducidad'] ?? null,
            ]);


            // Verificar que el producto se creó correctamente
            if (!$producto) {
                throw new Exception('No se pudo crear el producto');
            }

            // Si hay imagen o contiene, subirla
            if($request->hasFile('imagen')){
                try {
                    $this->subir_imagen($request, $producto->id);
                } catch (Exception $imageError) {
                    Log::error('Error al subir imagen: ' . $imageError->getMessage());
                    // Continuar aunque falle la imagen
                }
            }

            DB::commit();

            // Devolver los datos del producto creado para cuando se crea un producto con modal con ajax
            //Respuesta AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producto creado exitosamente',
                    'producto' => $producto
                ]);
            }

            // Respuesta para AJAX
            if($request->ajax()){
                return response()->json([
                    'success' => true,
                    'message' => 'Producto creado exitosamente',
                    'producto' => [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'codigo' => $producto->codigo,
                        'descripcion' => $producto->descripcion,
                        'categoria' => $producto->categoria->nombre ?? '',
                        'marca' => $producto->marca->nombre ?? '',
                        'proveedor' => $producto->proveedor->nombre ?? '',
                        'activo' => $producto->activo,
                        'requiere_fecha_caducidad' => $producto->requiere_fecha_caducidad,
                        'fecha_caducidad' => $producto->fecha_caducidad ? $producto->fecha_caducidad->format('d/m/Y') : null,
                        'imagen' => $producto->imagen ? asset('storage/' . $producto->imagen->ruta) : null,
                        'created_at' => $producto->created_at->format('d/m/Y H:i')
                    ]
                ]);
            }

            return redirect()->route('producto.index')->with('success', 'Producto creado exitosamente. Puedes realizar la compra más tarde usando el botón Comprar.!');

        }catch (Exception $e){
            DB::rollBack();
            Log::error('Error al guardar el producto: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Respuesta para AJAX
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el producto: ' . $e->getMessage(),
                    'error_details' => config('app.debug') ? $e->getTraceAsString() : null
                ], 500);
            }

            //return back()->with('error', 'Error: ' . $e->getMessage());//mostrar error completo
            //return redirect()->route('producto.index')->with('error', 'Error al guardar el producto.');

            // Respuesta para formulario tradicional
            return back()->withErrors(['error' => 'Error al guardar el producto: ' . $e->getMessage()])
                    ->withInput();

        }
    }

    // Función auxiliar para calcular dígito (necesaria para el generador interno)
    private function calcularDigitoVerificador($digits){
        $sum = 0;
        for ($i = 0; $i < strlen($digits); $i++) {
            $sum += ($digits[$i] * (($i % 2 === 0) ? 1 : 3)); // Lógica EAN para posiciones (impar*1, par*3)
            // Nota: Verifica tu lógica de par/impar según si el string empieza en index 0
        }
        return (10 - ($sum % 10)) % 10;
    }

    public function productCodeExists($number){
        return Producto::whereProductCode($number)->exists();
    }


    //Validar código EAN-13
    private function validateEAN13($ean13) {
        if (strlen($ean13) !== 13 || !ctype_digit($ean13)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int)$ean13[$i];
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit == (int)$ean13[12];
    }

    public function subir_imagen(Request $request, int $productoId):bool {
        if(!$request->hasFile('imagen')){

            return false;
        }

        // Opcional: borrar imagen anterior si existe
        $imagenExistente = Imagen::where('producto_id', $productoId)->first();
        if ($imagenExistente) {
            Storage::disk('public')->delete($imagenExistente->ruta);
            $imagenExistente->delete();
        }

        $rutaImagen = $request->file('imagen')->store('imagenes', 'public');

        return Imagen::create([
            'producto_id' => $productoId,
            'nombre' => basename($rutaImagen),
            'ruta' => $rutaImagen,

        ]) ? true : false;

    }

    public function update(Request $request, Producto $producto){

        // Validaciones
        $rules = [

            'categoria_id' => 'required|exists:categorias,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'marca_id'     => 'required|exists:marcas,id',
            'precio_venta' => 'required|numeric|min:0',

            'permite_mayoreo'         => 'boolean',
            'en_oferta'               => 'boolean',
            'precio_mayoreo'          => 'nullable|numeric|min:0',
            'precio_oferta'           => 'nullable|numeric|min:0',
            'cantidad_minima_mayoreo' => 'nullable|integer|min:1',
            'fecha_inicio_oferta'     => 'nullable|date',
            'fecha_fin_oferta'        => 'nullable|date|after_or_equal:fecha_inicio_oferta',

            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'required|string|max:255',
            'activo'       => 'required|boolean',
            'imagen'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            // 🔹 CAMPOS DE CADUCIDAD
            'requiere_fecha_caducidad' => 'boolean',
            'fecha_caducidad' => 'nullable|required_if:requiere_fecha_caducidad,1|date|after:today',
        ];

        // Mensajes personalizados
        $messages = [
            'fecha_caducidad.required_if' => 'La fecha de caducidad es obligatoria cuando se activa el control de caducidad.',
            'fecha_caducidad.after' => 'La fecha de caducidad debe ser posterior a hoy.',
        ];

        // Verificar si el producto puede ser editado
        $codigoEsEditable = $producto->codigoEsEditable();
        $codigoCambio = false; // Flag para saber si cambió el código

        if ($codigoEsEditable) {
            $rules['codigo'] = 'required|string|max:255|unique:productos,codigo,' . $producto->id;
        } else {
            // Si no es editable, verificar que no hayan intentado cambiarlo
            if ($request->has('codigo') && $request->codigo !== $producto->codigo) {
                return redirect()->back()
                    ->withErrors(['codigo' => 'No puedes cambiar el código de barras de un producto con ventas registradas.'])
                    ->withInput();
            }
        }


        try {
            $validated = $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        DB::beginTransaction();

        try{

            // Verificar si el código cambió
            if ($codigoEsEditable && isset($validated['codigo']) && $validated['codigo'] !== $producto->codigo) {
                $codigoCambio = true;
            }

            //Verificamos si la oferta sigue vigente
            $enOferta = $request->boolean('en_oferta');
            $fechaFin = $validated['fecha_fin_oferta'] ?? null;

            if ($enOferta && $fechaFin) {
                if (\Carbon\Carbon::parse($fechaFin)->isPast()) {
                    // Si la fecha fin ya pasó, desactivar oferta
                    $enOferta = false;
                }
            }

            // 🔹 Manejar fecha de caducidad
            $requiereCaducidad = $request->boolean('requiere_fecha_caducidad');
            $fechaCaducidad = $validated['fecha_caducidad'] ?? null;

            // Si se desactiva el control de caducidad, limpiar la fecha
            if (!$requiereCaducidad) {
                $fechaCaducidad = null;
            }

            // Preparar datos para actualizar
            $updateData = [

                'user_id' => Auth::id(),
                'categoria_id' => $validated['categoria_id'],
                'proveedor_id' => $validated['proveedor_id'],
                'marca_id'     => $validated['marca_id'],
                'nombre'       => $validated['nombre'],
                'descripcion'  => $validated['descripcion'],
                'activo'       => $validated['activo'],

                //Campos de precios
                'precio_venta' => $validated['precio_venta'] ?? 0,

                // 🔹 Mayoreo
                'permite_mayoreo'         => $request->boolean('permite_mayoreo'),
                'precio_mayoreo'          => $validated['precio_mayoreo'] ?? null,
                'cantidad_minima_mayoreo' => $validated['cantidad_minima_mayoreo'] ?? null,

                // 🔹 Oferta (con validación de vencimiento)
                'en_oferta'          => $enOferta,
                'precio_oferta'      => $validated['precio_oferta'] ?? null,
                'fecha_inicio_oferta'=> $validated['fecha_inicio_oferta'] ?? null,
                'fecha_fin_oferta'   => $fechaFin,

                // 🔹 Caducidad
                'requiere_fecha_caducidad' => $requiereCaducidad,
                'fecha_caducidad'          => $fechaCaducidad,
            ];

            // Solo incluir código si es editable
            if ($codigoEsEditable && isset($validated['codigo'])) {
                $updateData['codigo'] = $validated['codigo'];
            }

            // Actualizar el producto
            $producto->update($updateData);


            // Si cambió el código, regenerar el código de barras
            if ($codigoCambio) {
                $this->regenerarCodigoBarras($producto);
            }

            // Si se sube una nueva imagen, puedes opcionalmente eliminar la anterior
            if ($request->hasFile('imagen')) {
                $this->subir_imagen($request, $producto->id);
            }

            DB::commit();

            $mensaje = $codigoCambio ?
                'Producto actualizado exitosamente! Se ha generado un nuevo código de barras.' :
                'Producto actualizado exitosamente!';

            // Respuesta para AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $mensaje,
                    'data' => [
                        'producto' => $producto->load(['categoria', 'proveedor', 'marca', 'imagen']),
                        'codigo_cambio' => $codigoCambio
                    ]
                ], 200);
            }

            // Respuesta para formulario tradicional
            return redirect()->route('producto.index')->with('success', $mensaje);

        }catch(Exception $e){

            DB::rollBack();
            Log::error('Error al Actualizar el producto: ' . $e->getMessage());

            // Respuesta para AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el producto: ' . $e->getMessage(),
                    'error_details' => config('app.debug') ? $e->getTraceAsString() : null
                ], 500);
            }

            // Respuesta para formulario tradicional
            return redirect()->route('producto.index')->with('error', 'Error al Actualizar el producto!.' . $e->getMessage());

        }
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
    private function generarCodigoBarras($codigo){
        try {
            // Generar código de barras usando DNS1D
            $barcode = DNS1D::getBarcodePNG($codigo, 'EAN13');
            $barcodePath = 'barcodes/' . $codigo . '.png';
            $fullBarcodePath = public_path($barcodePath);

            // Crear directorio si no existe
            $barcodeDir = public_path('barcodes');
            if (!file_exists($barcodeDir)) {
                mkdir($barcodeDir, 0755, true);
            }

            // Guardar el archivo
            if (!file_put_contents($fullBarcodePath, base64_decode($barcode))) {
                throw new Exception('No se pudo generar el código de barras');
            }

            Log::info("Código de barras generado para código: {$codigo}");
            return $barcodePath;

        } catch (Exception $e) {
            Log::error("Error al generar código de barras para código {$codigo}: " . $e->getMessage());
            return null;
        }
    }


    //Regenera el código de barras para un producto
    private function regenerarCodigoBarras(Producto $producto){
        try {
            // Eliminar el código de barras anterior si existe
            if ($producto->barcode_path && file_exists(public_path($producto->barcode_path))) {
                unlink(public_path($producto->barcode_path));
                Log::info("Código de barras anterior eliminado: {$producto->barcode_path}");
            }

            // Generar nuevo código de barras
            $nuevaRuta = $this->generarCodigoBarras($producto->codigo);

            // Actualizar la ruta en la base de datos
            $producto->update(['barcode_path' => $nuevaRuta]);

            if ($nuevaRuta) {
                Log::info("Código de barras regenerado exitosamente para producto ID: {$producto->id}");
            } else {
                Log::warning("No se pudo regenerar el código de barras para producto ID: {$producto->id}");
            }

        } catch (Exception $e) {
            Log::error("Error al regenerar código de barras para producto ID {$producto->id}: " . $e->getMessage());
            // Establecer ruta como null si falla
            $producto->update(['barcode_path' => null]);
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
