<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
use App\Models\Producto;

class Reporte_productosController extends Controller
{
    //Reporte de productos
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

        return view('modulos.reportes.productos.index', compact('productos'));

    }
    //reporte falta stock productos
    public function falta_stock(){
        $titulo = "Falta Stock";
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
        ->whereBetween('productos.cantidad', [0,1])
        ->get();

        return view('modulos.reportes.productos.falta_stock', compact('productos'));
    }
}
