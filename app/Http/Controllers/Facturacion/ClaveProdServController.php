<?php

namespace App\Http\Controllers\Facturacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClaveProdServ;

class ClaveProdServController extends Controller
{
    //
    public function search(Request $request){
        $search = $request->input('term');
        $results = ClaveProdServ::where('clave', 'like', "%{$search}%")
            ->orWhere('descripcion', 'like', "%{$search}%")
            ->limit(20)
            ->get(['id', 'clave', 'descripcion']);

        return response()->json($results->map(function($item){
            return [
                'id' => $item->clave,
                'text' => "{$item->clave} - {$item->descripcion}"
            ];
        }));
    }
}
