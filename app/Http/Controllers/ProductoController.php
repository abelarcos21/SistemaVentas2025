<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

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
    }

    public function create(){

        return view('modulos.productos.create');

    }

    public function show(Producto $producto){

    }

    public function edit(Producto $proveedor){

        return view('modulos.productos.edit', compact('producto'));

    }

    public function store(Request $request){

        try{

            $validated = $request->validate([

                'nombre' => 'required|string|max:255',
                'telefono' => 'required|string|max:255',
                'email' => 'required|string|max:255',
                'codigo_postal' => 'required|string|max:255',
                'sitio_web' => 'required|string|max:255',
                'notas' => 'required|string|max:255',

            ]);


            Producto::create($validated);


            return redirect()->route('producto.index')->with('success', 'Producto Creado Correctamente');

        }catch(Exception $e){

            return redirect()->route('producto.index')->with('error', 'Error al Guardar!' . $e->getMessage());
        }



    }

    public function update(Request $request, Producto $producto){

        try{

            $validated = $request->validate([

                'nombre' => 'required|string|max:255',
                'telefono' => 'required|string|max:255',
                'email' => 'required|string|max:255',
                'codigo_postal' => 'required|string|max:255',
                'sitio_web' => 'required|string|max:255',
                'notas' => 'required|string|max:255',

            ]);

            $producto->fill($validated); // metodo fill es igual que el método save() pero sin crear un nuevo registro

            $producto->save();


            return redirect()->route('producto.index')->with('success', 'Producto Actualizado Correctamente');

        }catch(Exception $e){

            return redirect()->route('producto.index')->with('error', 'Error al Guardar!' . $e->getMessage());
        }

    }

    public function destroy(Proveedor $proveedor){

        $nombreProveedor = $proveedor->nombre;
        $proveedor->delete();
        return redirect()->route('proveedor.index')->with('success','El Proveedor  '.$nombreProveedor.'  se Elimino');

    }
}
