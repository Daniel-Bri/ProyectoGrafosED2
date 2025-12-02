<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function dashboard()
    {
        $totalLugares = DB::table('lugares')->count();
        $totalCaminos = DB::table('caminos')->count();
        
        // Obtener lugares con nombres de categoría
        $lugares = DB::table('lugares')
                    ->join('categoria_lugar', 'lugares.categoria_id', '=', 'categoria_lugar.id')
                    ->select('lugares.*', 'categoria_lugar.nombre as categoria_nombre')
                    ->get();
                    
        // Obtener categorías para el formulario
        $categorias = DB::table('categoria_lugar')->get();
        
        return view('dashboard', compact('totalLugares', 'totalCaminos', 'lugares', 'categorias'));
    }
}