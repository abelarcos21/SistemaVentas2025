<?php

namespace App\Http\Controllers\Facturacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClaveUnidad;

class ClaveUnidadController extends Controller
{
    //
    public function search(Request $request){
        $search = $request->input('term');
        $results = ClaveUnidad::where('clave', 'like', "%{$search}%")
            ->orWhere('nombre', 'like', "%{$search}%")
            ->limit(20)
            ->get(['id', 'clave', 'nombre']);

        return response()->json($results->map(function($item){
            return [
                'id' => $item->clave,
                'text' => "{$item->clave} - {$item->nombre}"
            ];
        }));
    }
}
