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
use Exception;
use Storage;


class ProductoController extends Controller
{
    //index
    public function index(){

        $productos = Producto::select(
            'productos.*',
            'categorias.nombre as nombre_categoria',
            'proveedores.nombre as nombre_proveedor',
            'imagens.ruta as imagen_producto',
            'imagens.id as imagen_id'
        )
        ->join('categorias', 'productos.categoria_id', '=' , 'categorias.id')
        ->join('proveedores', 'productos.proveedor_id', '=' , 'proveedores.id')
        ->leftJoin('imagens', 'productos.id', '=', 'imagens.producto_id')
        ->get();

        return view('modulos.productos.index', compact('productos'));

        /* $productos = Producto::with(['imagen', 'categoria', 'proveedor'])->get();

        return view('producto.index', compact('productos')); */
    }

    public function create(){
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();

        return view('modulos.productos.create', compact('categorias', 'proveedores'));

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
        return view('modulos.productos.edit', compact('producto','categorias','proveedores'));

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
            $query->where('nombre', 'LIKE', '%' . $request->busqueda . '%');
        }

        if ($request->filled('categoria_id') && $request->categoria_id !== 'todos') {
            $query->where('categoria_id', $request->categoria_id);
        }

        $productos = $query->where('cantidad', '>', 0)->get(); //Asegúrar de que los productos tengan cantidad > 0 en BD


        return response()->json([
            'html' => view('modulos.productos.listafiltrado', compact('productos'))->render(),
            'total' => $productos->count(),
        ]);

    }

    //IMPRIMIR ETIQUETAS DE CODIGO DE BARRAS
    public function imprimirEtiquetas(){
        $productos = Producto::orderBy('nombre')->get(); // o filtra como quieras
        return view('modulos.productos.etiquetas', compact('productos'));
    }

    public function store(Request $request){

        $validated = $request->validate([

            'categoria_id' => 'required',
            'proveedor_id' => 'required',
            'codigo' => 'nullable|string|max:255|unique:productos,codigo',// validación nullable si se deja en blanco el campo
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',// validación opcional de imagen

        ]);


        DB::beginTransaction();

        try{

            // Generar código si no se envía
            $codigo = $request->input('codigo') ?? str_pad((Producto::max('id') ?? 0) + 1, 8, '0', STR_PAD_LEFT);

            // Crear directorio si no existe
            $barcodeDir = public_path('barcodes');
            if (!file_exists($barcodeDir)) {
                mkdir($barcodeDir, 0777, true);
            }

            // Generar imagen de código de barras
            $barcode = DNS1D::getBarcodePNG($codigo, 'C128'); // Tipo C128 para mejor compatibilidad
            $barcodePath = 'barcodes/' . $codigo . '.png';
            file_put_contents(public_path($barcodePath), base64_decode($barcode));

            $producto = Producto::create([
                'user_id' => Auth::id(),
                'categoria_id' => $validated['categoria_id'],
                'proveedor_id' => $validated['proveedor_id'],
                'codigo'       => $codigo,
                'barcode_path' => $barcodePath,
                'nombre'       => $validated['nombre'],
                'descripcion'  => $validated['descripcion'],

            ]);

            //SI HAY CONTIENE IMAGEN SUBIRLA
            if($request->hasFile('imagen')){
                $this->subir_imagen($request, $producto->id);
            }

            DB::commit();

            return redirect()->route('producto.index')->with('success', 'Producto creado exitosamente!');

        }catch (Exception $e){
            DB::rollBack();
            Log::error('Error al guardar el producto: ' . $e->getMessage());
            //return back()->with('error', 'Error: ' . $e->getMessage());//mostrar error completo
            return redirect()->route('producto.index')->with('error', 'Error al guardar el producto.');
        }
    }

    public function productCodeExists($number){
        return Producto::whereProductCode($number)->exists();
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

        $validated = $request->validate([

            'categoria_id' => 'required',
            'proveedor_id' => 'required',
            'codigo' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'precio_venta' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',// validación opcional de imagen

        ]);

        DB::beginTransaction();

        try{

            //LLAMAR EL METODO ASI PORQUE YA SE ESTA INYECTANDO EL PRODUCTO($PRODUCTO) MODEL BINDING
            $producto->update([
                'user_id' => Auth::id(),
                'categoria_id' => $validated['categoria_id'],
                'proveedor_id' => $validated['proveedor_id'],
                'codigo'       => $validated['codigo'],
                'nombre'       => $validated['nombre'],
                'descripcion'  => $validated['descripcion'],
                'precio_venta'  => $validated['precio_venta'],

            ]);

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
