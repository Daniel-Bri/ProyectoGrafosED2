<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LugarController extends Controller
{
    public function index()
    {
        // ✅ AHORA SÍ FUNCIONARÁ - Todas las columnas existen
        $lugares = DB::table('lugares')
                    ->join('categoria_lugar', 'lugares.categoria_id', '=', 'categoria_lugar.id')
                    ->select('lugares.*', 'categoria_lugar.nombre as categoria_nombre')
                    ->get();

        $categorias_count = DB::table('categoria_lugar')->count();
        
        return view('lugares.index', compact('lugares', 'categorias_count'));
    }

    public function create()
    {
        $categorias = DB::table('categoria_lugar')->get();
        return view('lugares.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'categoria_id' => 'required|exists:categoria_lugar,id'
        ]);

        DB::table('lugares')->insert([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'x' => $request->x,
            'y' => $request->y,
            'categoria_id' => $request->categoria_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('lugares.index')
            ->with('success', 'Lugar creado exitosamente.');
    }

    public function show($id)
    {
        $lugar = DB::table('lugares')
                ->join('categoria_lugar', 'lugares.categoria_id', '=', 'categoria_lugar.id')
                ->where('lugares.id', $id)
                ->select('lugares.*', 'categoria_lugar.nombre as categoria_nombre')
                ->first();

        // Obtener caminos conectados
        $caminosOrigen = DB::table('caminos')
                        ->join('lugares as destino', 'caminos.lugar_destino_id', '=', 'destino.id')
                        ->where('caminos.lugar_origen_id', $id)
                        ->select('caminos.*', 'destino.nombre as destino_nombre')
                        ->get();

        $caminosDestino = DB::table('caminos')
                        ->join('lugares as origen', 'caminos.lugar_origen_id', '=', 'origen.id')
                        ->where('caminos.lugar_destino_id', $id)
                        ->select('caminos.*', 'origen.nombre as origen_nombre')
                        ->get();

        return view('lugares.show', compact('lugar', 'caminosOrigen', 'caminosDestino'));
    }

    public function edit($id)
    {
        $lugar = DB::table('lugares')->where('id', $id)->first();
        $categorias = DB::table('categoria_lugar')->get();
        
        return view('lugares.edit', compact('lugar', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'categoria_id' => 'required|exists:categoria_lugar,id'
        ]);

        DB::table('lugares')->where('id', $id)->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'x' => $request->x,
            'y' => $request->y,
            'categoria_id' => $request->categoria_id,
            'updated_at' => now(),
        ]);

        return redirect()->route('lugares.index')
            ->with('success', 'Lugar actualizado exitosamente.');
    }

    public function destroy($id)
    {
        DB::table('lugares')->where('id', $id)->delete();
        return redirect()->route('lugares.index')
            ->with('success', 'Lugar eliminado exitosamente.');
    }

    public function eliminar($id)
    {
        $lugar = DB::table('lugares')
                  ->join('categoria_lugar', 'lugares.categoria_id', '=', 'categoria_lugar.id')
                  ->where('lugares.id', $id)
                  ->select('lugares.*', 'categoria_lugar.nombre as categoria_nombre')
                  ->first();
                  
        return view('lugares.eliminar', compact('lugar'));
    }
}