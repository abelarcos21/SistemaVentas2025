<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller{

    public function index(){
        return response()->json(Producto::all());
    }

    public function store(Request $request){

        $validated = $request->validate([
        'nombre' => 'required|string',
        'precio' => 'required|numeric',
        'stock' => 'required|integer',
        ]);

        $producto = Producto::create($validated);

        return response()->json($producto, 201);

    }



    public function show($id){
        return response()->json(Producto::findOrFail($id));
    }

    public function update(Request $request, $id){
        $product = Producto::findOrFail($id);
        $product->update($request->all());
        return response()->json($product);
    }

    public function destroy($id){
        Producto::destroy($id);
        return response()->json(null, 204);
    }



}
