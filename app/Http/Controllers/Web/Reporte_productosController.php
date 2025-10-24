<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // 👈 IMPORTANTE: esta línea importa la clase base
use App\Models\Producto;
use Yajra\DataTables\Facades\DataTables;

class Reporte_productosController extends Controller
{
    //Reporte de productos
    public function index(Request $request){
        if ($request->ajax()) {
            $productos = Producto::select(
                'productos.id',
                'productos.codigo',
                'productos.nombre',
                'productos.descripcion',
                'productos.cantidad',
                'productos.precio_venta',
                'productos.precio_compra',
                'categorias.nombre as nombre_categoria',
                'proveedores.nombre as nombre_proveedor',
                'imagens.ruta as imagen_ruta'
            )
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->join('proveedores', 'productos.proveedor_id', '=', 'proveedores.id')
            ->leftJoin('imagens', 'productos.id', '=', 'imagens.producto_id');

            return DataTables::of($productos)
                ->addIndexColumn()
                ->addColumn('nro', function ($producto) {
                    return $producto->id;
                })
                ->addColumn('categoria', function ($producto) {
                    return $producto->nombre_categoria;
                })
                ->addColumn('proveedor', function ($producto) {
                    return $producto->nombre_proveedor;
                })
                ->editColumn('codigo', function ($producto) {
                    return '<code>' . $producto->codigo . '</code><br>';
                })
                ->addColumn('nombre', function ($producto) {
                    return $producto->nombre;
                })
                ->addColumn('descripcion', function ($producto) {
                    return $producto->descripcion;
                })
                ->addColumn('imagen', function ($producto) {
                    $ruta = $producto->imagen_ruta
                        ? asset('storage/' . $producto->imagen_ruta)
                        : asset('images/placeholder-caja.png');

                    return '<a href="#" class="ver-imagen" data-imagen="' . $ruta . '" data-nombre="' . $producto->nombre . '">
                                <img src="' . $ruta . '"
                                     width="50" height="50"
                                     class="img-thumbnail rounded shadow"
                                     style="object-fit: cover;">
                            </a>';
                })
                ->addColumn('stock', function ($producto) {
                    if ($producto->cantidad > 5) {
                        return '<span class="badge bg-success">' . $producto->cantidad . '</span>';
                    } else {
                        return '<span class="badge bg-danger">' . $producto->cantidad . '</span>';
                    }
                })
                ->addColumn('precio_venta', function ($producto) {
                    return '<span class="text-primary">$' . number_format($producto->precio_venta, 2) . '</span>';
                })
                ->addColumn('precio_compra', function ($producto) {
                    return '<span class="text-primary">$' . number_format($producto->precio_compra, 2) . '</span>';
                })
                /* ->addColumn('utilidad', function ($producto) {
                    $utilidad = $producto->precio_venta - $producto->precio_compra;
                    $margen = $producto->precio_venta > 0
                        ? (($utilidad / $producto->precio_venta) * 100)
                        : 0;

                    return '<div class="text-center">
                                <span class="badge bg-success">$' . number_format($utilidad, 2) . '</span>
                                <br>
                                <small class="text-muted">(' . number_format($margen, 1) . '%)</small>
                            </div>';
                }) */
                ->filterColumn('nombre_categoria', function($query, $keyword) {
                    $query->where('categorias.nombre', 'like', "%{$keyword}%");
                })
                ->filterColumn('nombre_proveedor', function($query, $keyword) {
                    $query->where('proveedores.nombre', 'like', "%{$keyword}%");
                })
                ->rawColumns(['imagen', 'stock', 'precio_venta', 'precio_compra','codigo'])
                ->make(true);
        }

        return view('modulos.reportes.productos.index');
    }

    //reporte falta stock productos
    /* public function falta_stock(){
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
    } */

    //reporte falta stock productos
    public function falta_stock(Request $request){
        if ($request->ajax()) {
            $productos = Producto::select(
                'productos.id',
                'productos.codigo',
                'productos.nombre',
                'productos.descripcion',
                'productos.cantidad',
                'productos.precio_venta',
                'productos.precio_compra',
                'categorias.nombre as nombre_categoria',
                'proveedores.nombre as nombre_proveedor',
                'imagens.ruta as imagen_ruta'
            )
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->join('proveedores', 'productos.proveedor_id', '=', 'proveedores.id')
            ->leftJoin('imagens', 'productos.id', '=', 'imagens.producto_id')
            ->whereBetween('productos.cantidad', [0, 1]);

            return DataTables::of($productos)
                ->addIndexColumn()
                ->addColumn('nro', function ($producto) {
                    return $producto->id;
                })
                ->addColumn('categoria', function ($producto) {
                    return $producto->nombre_categoria;
                })
                ->addColumn('proveedor', function ($producto) {
                    return $producto->nombre_proveedor;
                })
                ->editColumn('codigo', function ($producto) {
                    return '<code>' . $producto->codigo . '</code><br>';
                })
                ->addColumn('nombre', function ($producto) {
                    return $producto->nombre;
                })
                ->addColumn('descripcion', function ($producto) {
                    return $producto->descripcion;
                })
                ->addColumn('imagen', function ($producto) {
                    $ruta = $producto->imagen_ruta
                        ? asset('storage/' . $producto->imagen_ruta)
                        : asset('images/placeholder-caja.png');

                    return '<a href="#" class="ver-imagen" data-imagen="' . $ruta . '" data-nombre="' . $producto->nombre . '">
                                <img src="' . $ruta . '"
                                     width="50" height="50"
                                     class="img-thumbnail rounded shadow"
                                     style="object-fit: cover;">
                            </a>';
                })
                ->addColumn('stock', function ($producto) {
                    return '<span class="badge bg-danger">' . $producto->cantidad . '</span>';
                })
                ->addColumn('precio_venta', function ($producto) {
                    return '<span class="text-primary">$' . number_format($producto->precio_venta, 2) . '</span>';
                })
                ->addColumn('precio_compra', function ($producto) {
                    return '<span class="text-primary">$' . number_format($producto->precio_compra, 2) . '</span>';
                })
                /* ->addColumn('utilidad', function ($producto) {
                    $utilidad = $producto->precio_venta - $producto->precio_compra;
                    $margen = $producto->precio_venta > 0
                        ? (($utilidad / $producto->precio_venta) * 100)
                        : 0;

                    return '<div class="text-center">
                                <span class="badge bg-warning text-dark">$' . number_format($utilidad, 2) . '</span>
                                <br>
                                <small class="text-muted">(' . number_format($margen, 1) . '%)</small>
                            </div>';
                }) */
                ->filterColumn('nombre_categoria', function($query, $keyword) {
                    $query->where('categorias.nombre', 'like', "%{$keyword}%");
                })
                ->filterColumn('nombre_proveedor', function($query, $keyword) {
                    $query->where('proveedores.nombre', 'like', "%{$keyword}%");
                })
                ->rawColumns(['imagen', 'stock', 'precio_venta', 'precio_compra','codigo'])
                ->make(true);
        }


        return view('modulos.reportes.productos.falta_stock');
    }
}
