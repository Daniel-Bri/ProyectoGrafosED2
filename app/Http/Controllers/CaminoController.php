<?php

namespace App\Http\Controllers;

use App\Models\Camino;
use App\Models\Lugar;
use Illuminate\Http\Request;

class CaminoController extends Controller
{
    public function index()
    {
        $caminos = Camino::with(['origen', 'destino'])->get();
        return view('caminos.index', compact('caminos'));
    }

    public function create()
    {
        $lugares = Lugar::all();
        return view('caminos.create', compact('lugares'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lugar_origen_id' => 'required|exists:lugares,id',
            'lugar_destino_id' => 'required|exists:lugares,id|different:lugar_origen_id',
            'distancia' => 'required|numeric|min:0',
            'es_bidireccional' => 'boolean'
        ]);

        Camino::create($request->all());

        return redirect()->route('caminos.index')
            ->with('success', 'Camino creado exitosamente.');
    }

    public function show(Camino $camino)
    {
        return view('caminos.show', compact('camino'));
    }

    public function edit(Camino $camino)
    {
        $lugares = Lugar::all();
        return view('caminos.edit', compact('camino', 'lugares'));
    }

    public function update(Request $request, Camino $camino)
    {
        $request->validate([
            'lugar_origen_id' => 'required|exists:lugares,id',
            'lugar_destino_id' => 'required|exists:lugares,id|different:lugar_origen_id',
            'distancia' => 'required|numeric|min:0',
            'es_bidireccional' => 'boolean'
        ]);

        $camino->update($request->all());

        return redirect()->route('caminos.index')
            ->with('success', 'Camino actualizado exitosamente.');
    }

    public function destroy(Camino $camino)
    {
        $camino->delete();
        return redirect()->route('caminos.index')
            ->with('success', 'Camino eliminado exitosamente.');
    }

    public function eliminar($id)
    {
        $camino = Camino::findOrFail($id);
        return view('caminos.eliminar', compact('camino'));
    }
}