<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LugarController;
use App\Http\Controllers\CaminoController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\Controller;

Route::get('/', [Controller::class, 'dashboard'])->name('dashboard');

// Rutas para Lugares - Resource con rutas adicionales
Route::prefix('lugares')->name('lugares.')->group(function () {
    Route::get('/', [LugarController::class, 'index'])->name('index');
    Route::get('/create', [LugarController::class, 'create'])->name('create');
    Route::post('/', [LugarController::class, 'store'])->name('store');
    Route::get('/{lugar}', [LugarController::class, 'show'])->name('show');
    Route::get('/{lugar}/edit', [LugarController::class, 'edit'])->name('edit');
    Route::put('/{lugar}', [LugarController::class, 'update'])->name('update');
    Route::delete('/{lugar}', [LugarController::class, 'destroy'])->name('destroy');
    Route::get('/{lugar}/eliminar', [LugarController::class, 'eliminar'])->name('eliminar');
});

// Rutas para Caminos - Resource con rutas adicionales
Route::prefix('caminos')->name('caminos.')->group(function () {
    Route::get('/', [CaminoController::class, 'index'])->name('index');
    Route::get('/create', [CaminoController::class, 'create'])->name('create');
    Route::post('/', [CaminoController::class, 'store'])->name('store');
    Route::get('/{camino}', [CaminoController::class, 'show'])->name('show');
    Route::get('/{camino}/edit', [CaminoController::class, 'edit'])->name('edit');
    Route::put('/{camino}', [CaminoController::class, 'update'])->name('update');
    Route::delete('/{camino}', [CaminoController::class, 'destroy'])->name('destroy');
    Route::get('/{camino}/eliminar', [CaminoController::class, 'eliminar'])->name('eliminar');
});

// Rutas para cálculo de rutas
Route::prefix('rutas')->name('rutas.')->group(function () {
    Route::get('/calcular', [RutaController::class, 'calcular'])->name('calcular');
    Route::post('/calcular-ruta', [RutaController::class, 'calcularRuta'])->name('calcular-ruta');
    Route::get('/mostrar/{origen}/{destino}', [RutaController::class, 'mostrarRuta'])->name('mostrar');
});

// API para el mapa (puedes mover esto a api.php si prefieres)
Route::get('/api/mapa/datos', function () {
    $lugares = App\Models\Lugar::all();
    $caminos = App\Models\Camino::with(['origen', 'destino'])->get();
    
    return response()->json([
        'lugares' => $lugares,
        'caminos' => $caminos
    ]);
})->name('api.mapa.datos');


Route::get('/test/dijkstra', function() {
    try {
        \Log::info('=== TEST DIJKSTRA MANUAL ===');
        
        // Crear grafo simple de prueba
        $grafo = new \App\Services\Grafos\Pesados\GrafoPesado();
        
        // Crear 3 lugares de prueba
        $lugar1 = (object)['id' => 1, 'nombre' => 'A', 'x' => 0, 'y' => 0];
        $lugar2 = (object)['id' => 2, 'nombre' => 'B', 'x' => 10, 'y' => 0];
        $lugar3 = (object)['id' => 3, 'nombre' => 'C', 'x' => 20, 'y' => 0];
        
        \Log::info('Insertando vértices...');
        $grafo->insertarVertice($lugar1);
        $grafo->insertarVertice($lugar2);
        $grafo->insertarVertice($lugar3);
        
        \Log::info('Insertando aristas...');
        $grafo->insertarArista($lugar1, $lugar2, 10.0);
        $grafo->insertarArista($lugar2, $lugar3, 20.0);
        
        \Log::info('Creando Dijkstra...');
        $dijkstra = new \App\Services\Grafos\Pesados\Dijkstra($grafo, $lugar1);
        
        \Log::info('Calculando ruta A->C...');
        $resultado = $dijkstra->getCaminoMasCorto($lugar3);
        
        return response()->json([
            'success' => true,
            'grafo_vertices' => count($grafo->getVertices()),
            'distancia' => $resultado['costo'],
            'ruta' => array_map(function($l) { return $l->nombre; }, $resultado['ruta']),
            'ruta_ids' => array_map(function($l) { return $l->id; }, $resultado['ruta'])
        ]);
        
    } catch (\Exception $e) {
        \Log::error('ERROR en test Dijkstra:', [
            'mensaje' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});