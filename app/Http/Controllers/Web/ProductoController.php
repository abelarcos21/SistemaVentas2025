<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
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


class ProductoController extends Controller
{
    //constructor
    function __construct(){
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
        $this->middleware('permission:product-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    //index
    public function index(){

        $categorias = Categoria::all();
        $proveedores = Proveedor::all();
        $marcas = Marca::all();

        $productos = Producto::select(
            'productos.id',
            'productos.nombre',
            'productos.descripcion',
            'productos.precio_compra',
            'productos.precio_venta',
            'productos.cantidad',
            'productos.moneda',
            'productos.codigo',
            'productos.barcode_path',
            'productos.categoria_id',
            'productos.proveedor_id',
            'productos.marca_id',
            'productos.created_at',
            'productos.activo',
            // otros campos que necesites de productos

            'categorias.nombre as nombre_categoria',
            'proveedores.nombre as nombre_proveedor',
            'marcas.nombre as nombre_marca',
            'imagens.ruta as imagen_producto',
            'imagens.id as imagen_id'
        )
        ->join('categorias', 'productos.categoria_id', '=' , 'categorias.id')
        ->join('proveedores', 'productos.proveedor_id', '=' , 'proveedores.id')
        ->join('marcas', 'productos.marca_id', '=', 'marcas.id')
        ->leftJoin('imagens', 'productos.id', '=', 'imagens.producto_id')
        ->with('monedas') // 👈 importante: carga el modelo relacionado correctamente
        ->get();

        return view('modulos.productos.index', compact('productos', 'categorias', 'proveedores', 'marcas'));

        /* $productos = Producto::with(['imagen', 'categoria', 'proveedor'])->get();

        return view('producto.index', compact('productos')); */
    }

    public function create(){
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();
        $marcas = Marca::all();

        return view('modulos.productos.create', compact('categorias', 'proveedores','marcas'));

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

    //BUSCAR PRODUCTO POR CODIGO PARA VERIFICAR SI EXISTE CUANDO SE ESCANEA UN PRODUCTO EN LA VISTA INDEX DE PRODUCTOS
    public function buscar(Request $request){
        $producto = Producto::where('codigo', $request->codigo)->first();

        return response()->json([
            'existe' => $producto !== null
        ]);
    }

    //CAMBIAR ESTADO DE PRODUCTO DE ACTIVO
    public function cambiarEstado(Request $request, $id){
        $producto = Producto::findOrFail($id);
        $producto->activo = $request->activo;
        $producto->save();
        return response()->json(['message' => 'Estado Actualizado Correctamente']);
    }

    //FILTRAR LOS PRODUCTOS Y LAS CATEGORIAS
    public function filtrar(Request $request){

       $query = Producto::query();

        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;

             // Si parece un código de barras (solo números y más de 8 dígitos)
            if (preg_match('/^\d{8,}$/', $busqueda)) {
                // Búsqueda exacta por código
                $query->where(function($q) use ($busqueda) {
                    $q->where('codigo', $busqueda);

                });
            } else {
                // Búsqueda normal por nombre y códigos (con LIKE)
                $query->where(function($q) use ($busqueda) {
                    $q->where('nombre', 'LIKE', '%' . $busqueda . '%')
                    ->orWhere('codigo', 'LIKE', '%' . $busqueda . '%');
                });
            }

        }

        if ($request->filled('categoria_id') && $request->categoria_id !== 'todos') {
            $query->where('categoria_id', $request->categoria_id);
        }

        $productos = $query->where('cantidad', '>=', 0)->get(); //Asegúrar de que los productos tengan cantidad > 0 en BD


        return response()->json([
            'html' => view('modulos.productos.listafiltrado', compact('productos'))->render(),
            'total' => $productos->count(),
            /* 'paginacion' => $productos->links()->render(), */

        ]);

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

    //IMPRIMIR ETIQUETAS DE CODIGO DE BARRAS
    public function imprimirEtiquetas(){
        $productos = Producto::orderBy('nombre')->get(); // o filtra como quieras
        return view('modulos.productos.etiquetas', compact('productos'));
    }

    public function store(Request $request){

        $validated = $request->validate([

            'categoria_id' => 'required|exists:categorias,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'marca_id' => 'required|exists:marcas,id',
            'codigo' => 'nullable|string|max:255|unique:productos,codigo',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'activo' => 'required|boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',// validación opcional de imagen max en KB

        ]);


        DB::beginTransaction();

        try{

            $codigo = null;
            //si se envio un codigo manual
            if(!empty($validated['codigo'])){
                $codigo = $validated['codigo'];
                //validar que sea numerico de 13 digitos
                if(!preg_match('/^\d{13}$/', $codigo)){
                    DB::rollBack();
                    return back()->withErrors(['codigo' => 'El código debe tener exactamente 13 dígitos numéricos.'])
                             ->withInput();
                }

                // Validar dígito verificador EAN-13
                if(!$this->validateEAN13($codigo)){
                    DB::rollBack();
                    return back()->withErrors(['codigo' => 'El código EAN-13 no es válido (dígito verificador incorrecto).'])
                                ->withInput();
                }

            }else{

                //Generar codigo EAN-13 valido y unico
                do{

                    $base12 = '750' . str_pad(random_int(0, 999999999), 9, '0', STR_PAD_LEFT);

                    $suma = 0;
                    for ($i = 0; $i < 12; $i++) {
                        $digito = (int)$base12[$i];
                        $suma += ($i % 2 === 0) ? $digito : $digito * 3;
                    }

                    $verificador = (10 - ($suma % 10)) % 10;
                    $codigo = $base12 . $verificador;

                } while (Producto::where('codigo', $codigo)->exists());
            }

            // Generar código si no se envía
            //$codigo = $request->input('codigo') ?? str_pad((Producto::max('id') ?? 0) + 1, 8, '0', STR_PAD_LEFT);

            // Crear directorio si no existe
            $barcodeDir = public_path('barcodes');
            if (!file_exists($barcodeDir)) {
                mkdir($barcodeDir, 0777, true);
            }

            // Generar imagen de código de barras
            try {
                $barcode = DNS1D::getBarcodePNG($codigo, 'EAN13');
                $barcodePath = 'barcodes/' . $codigo . '.png';
                $fullBarcodePath = public_path($barcodePath);

                if(!file_put_contents($fullBarcodePath, base64_decode($barcode))) {
                    throw new Exception('No se pudo generar el código de barras');
                }

            } catch (Exception $barcodeError) {
                Log::error('Error al generar código de barras: ' . $barcodeError->getMessage());
                $barcodePath = null; // Continuar sin código de barras
            }

            // Crear el producto
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

            ]);


            // Verificar que el producto se creó correctamente
            if (!$producto) {
                throw new Exception('No se pudo crear el producto');
            }

            //SI HAY CONTIENE IMAGEN SUBIRLA
            /* if($request->hasFile('imagen')){
                $this->subir_imagen($request, $producto->id);
            } */

            // Si hay imagen, subirla
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
            if($request->ajax()){

                return response()->json([
                    'success' => true,
                    'producto' => [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'codigo' => $producto->codigo,
                        'descripcion' => $producto->descripcion,
                        'categoria_id' => $producto->categoria->nombre ?? '',
                        'marca_id' => $producto->marca->nombre ?? '',
                        'proveedor_id' => $producto->proveedor->nombre ?? '',
                        'precio_venta' => $producto->precio_venta,
                        'precio_compra' => $producto->precio_compra,
                        'cantidad' => $producto->cantidad,
                        'activo' => $producto->activo,
                        'imagen' => $producto->imagen,
                        'moneda' => $producto->monedas->codigo ?? 'BOB',
                        'created_at' => $producto->created_at
                    ]
                ]);

            }

            return redirect()->route('producto.index')->with('success', 'Producto creado exitosamente. Puedes realizar la compra más tarde usando el botón Comprar.!');

        }catch (Exception $e){
            DB::rollBack();
            Log::error('Error al guardar el producto: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            //return back()->with('error', 'Error: ' . $e->getMessage());//mostrar error completo
            //return redirect()->route('producto.index')->with('error', 'Error al guardar el producto.');
            return back()->withErrors(['error' => 'Error al guardar el producto: ' . $e->getMessage()])
                    ->withInput();
        }
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

        // Validaciones básicas
        $rules = [
            'categoria_id' => 'required|exists:categorias,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'marca_id' => 'required|exists:marcas,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'activo' => 'required|boolean',
            'precio_venta' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Verificar si el producto puede ser editado
        $codigoEsEditable = $producto->codigoEsEditable();

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

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try{

            // Preparar datos para actualizar
            $updateData = [
                'user_id' => Auth::id(),
                'categoria_id' => $validated['categoria_id'],
                'proveedor_id' => $validated['proveedor_id'],
                'marca_id' => $validated['marca_id'],
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'],
                'activo' => $validated['activo'],
                'precio_venta' => $validated['precio_venta'],
            ];

            // Solo incluir código si es editable
            if ($codigoEsEditable && isset($validated['codigo'])) {
                $updateData['codigo'] = $validated['codigo'];
            }

            // Actualizar el producto
            $producto->update($updateData);

            // Si se sube una nueva imagen, puedes opcionalmente eliminar la anterior aquí
            if ($request->hasFile('imagen')) {
                $this->subir_imagen($request, $producto->id);
            }

            DB::commit();

            return redirect()->route('producto.index')->with('success', 'Producto Actualizado exitosamente!');

        }catch(Exception $e){

            DB::rollBack();
            Log::error('Error al Actualizar el producto: ' . $e->getMessage());
            return redirect()->route('producto.index')->with('error', 'Error al Actualizar el producto.');

        }




        /* $producto->fill($validated); // metodo fill es igual que el método save() pero sin crear un nuevo registro

        $producto->save();


        return redirect()->route('producto.index')->with('success', 'Producto Actualizado Correctamente');



        return redirect()->route('producto.index')->with('error', 'Error al Guardar!' . $e->getMessage()); */


    }

    public function destroy(Producto $producto){

        DB::beginTransaction();

        try{

            //eliminar la imagen si existe
            $imagen = $producto->imagen;

            if($imagen){
                Storage::disk('public')->delete($imagen->ruta);//elimina del disco
                $imagen->delete();//eliminar de la BD
            }

            $nombreProducto = $producto->nombre;//nombre del producto
            $producto->delete();//elimina el producto

            DB::commit();
            return redirect()->route('producto.index')->with('success','Producto  '.$nombreProducto.' Eliminado Correctamente');
        }catch(Exception $e){
            DB::rollBack();
            Log::error('Error al eliminar producto: ' . $e->getMessage());
            return redirect()->route('producto.index')->with('error', 'Ocurrió un error al eliminar el producto.');

        }
    }
}
