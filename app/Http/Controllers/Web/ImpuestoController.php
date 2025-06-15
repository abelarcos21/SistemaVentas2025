<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImpuestoController extends Controller
{
    //

    public function index()
    {
        return view('impuestos.index');
    }

    public function list()
    {
        return datatables()->of(Impuesto::query())
            ->addColumn('actions', function ($i) {
                return view('impuestos.partials.actions', compact('i'));
            })
            ->toJson();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required',
            'impuesto' => 'required|in:ISR,IVA,IEPS',
            'tipo' => 'required|in:Traslado,Retención',
            'factor' => 'required|in:Tasa,Cuota,Exento',
            'tasa' => 'required|numeric',
        ]);

        Impuesto::create($data);
        return response()->json(['success' => true]);
    }

    public function edit(Impuesto $impuesto)
    {
        return response()->json($impuesto);
    }

    public function update(Request $request, Impuesto $impuesto)
    {
        $data = $request->validate([
            'nombre' => 'required',
            'impuesto' => 'required|in:ISR,IVA,IEPS',
            'tipo' => 'required|in:Traslado,Retención',
            'factor' => 'required|in:Tasa,Cuota,Exento',
            'tasa' => 'required|numeric',
        ]);

        $impuesto->update($data);
        return response()->json(['success' => true]);
    }

    public function destroy(Impuesto $impuesto)
    {
        $impuesto->delete();
        return response()->json(['success' => true]);
    }
}
