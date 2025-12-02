<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Services\Grafos\Pesados\Dijkstra;
// use App\Services\Grafos\Pesados\GrafoPesado;

class RutaController extends Controller
{
    public function calcular()
    {
        // ✅ Usar DB facade directamente para evitar problemas del modelo
        $lugares = \Illuminate\Support\Facades\DB::table('lugares')->get();
        return view('rutas.calcular', compact('lugares'));
    }

    public function calcularRuta(Request $request)
    {
        $request->validate([
            'origen_id' => 'required|exists:lugares,id',
            'destino_id' => 'required|exists:lugares,id|different:origen_id'
        ]);

        // ✅ Datos de ejemplo temporalmente (comenta los grafos)
        $ruta = [
            'distancia_total' => 350.5,
            'camino' => [
                'Entrada Principal',
                'Facultad de Ciencias Exactas', 
                'Biblioteca Central',
                'Destino Final'
            ]
        ];

        return redirect()->route('rutas.mostrar', [
            'origen' => $request->origen_id,
            'destino' => $request->destino_id
        ])->with('ruta', $ruta);

        /*
        // Código original (comentado temporalmente)
        $grafo = $this->construirGrafoDesdeBD();
        $dijkstra = new Dijkstra($grafo);
        $ruta = $dijkstra->calcularRuta($request->origen_id, $request->destino_id);
        */
    }

    public function mostrarRuta($origen, $destino)
    {
        // ✅ Usar DB facade directamente
        $origenLugar = \Illuminate\Support\Facades\DB::table('lugares')->where('id', $origen)->first();
        $destinoLugar = \Illuminate\Support\Facades\DB::table('lugares')->where('id', $destino)->first();
        $ruta = session('ruta');

        return view('rutas.mostrar', compact('origenLugar', 'destinoLugar', 'ruta'));
    }

    private function construirGrafoDesdeBD()
    {
        // Comentado temporalmente
        return null;
    }
}