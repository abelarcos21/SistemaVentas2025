<?php

namespace App\Http\Controllers\Facturacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{
    //
    public function index() {
        $productos = Producto::all();
        return view('modulos.facturas.catalogos.productos.index', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([

            'clave_prod_serv' => 'nullable|string|max:10',
            'clave_unidad' => 'nullable|string|max:5',
            'unidad_descripcion' => 'nullable|string',
            'precio_unitario' => 'nullable|numeric|min:0',
            'impuesto_trasladado' => 'nullable|string|max:3',
            'tasa_o_cuota' => 'nullable|numeric|min:0|max:1',
            'tipo_factor' => 'nullable|in:Tasa,Cuota,Exento',
            'objeto_imp' => 'required|in:01,02,03',
            'numero_identificacion' => 'nullable|string',
        ]);

        $producto = Producto::create($request->only([
            'clave_prod_serv',
            'clave_unidad',
            'unidad_descripcion',
            'precio_unitario',
            'impuesto_trasladado',
            'tasa_o_cuota',
            'tipo_factor',
            'objeto_imp',
            'numero_identificacion'
        ]));

        return redirect()->route('facturacion.productos.index')
        ->with('success', 'Producto creado correctamente');

        return response()->json(['success' => true, 'producto' => $producto]);
    }

    public function show($id)
    {
        return response()->json(Producto::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'clave_prod_serv' => 'required',
            'descripcion' => 'required',
            'clave_unidad' => 'required',
            'unidad' => 'required',
            'numero_identificacion' => 'nullable|string',
            'precio' => 'required|numeric',
        ]);

        $producto->update($request->only([
            'clave_prod_serv',
            'descripcion',
            'clave_unidad',
            'unidad',
            'numero_identificacion',
            'precio'
        ]));

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Producto::destroy($id);
        return response()->json(['success' => true]);
    }

    public function list()
    {
        return datatables()->of(Producto::select('id', 'clave_prod_serv', 'descripcion', 'clave_unidad', 'unidad', 'numero_identificacion', 'precio'))
            ->addColumn('acciones', function ($producto) {
                return view('facturacion.productos.partials.acciones', compact('producto'))->render();
            })
            ->rawColumns(['acciones'])
            ->toJson();
    }
}
