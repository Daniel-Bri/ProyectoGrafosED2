<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Lugar;
use App\Models\Camino;

Route::get('/mapa/datos', function (Request $request) {
    $lugares = Lugar::all();
    $caminos = Camino::with(['origen', 'destino'])->get();
    
    return response()->json([
        'lugares' => $lugares,
        'caminos' => $caminos
    ]);
});