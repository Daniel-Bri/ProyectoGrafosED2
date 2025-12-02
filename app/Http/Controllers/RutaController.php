<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Grafos\Pesados\Dijkstra;
use App\Services\Grafos\Pesados\GrafoPesado;

class RutaController extends Controller
{
    public function calcular()
    {
        $lugares = DB::table('lugares')->get();
        return view('rutas.calcular', compact('lugares'));
    }

   public function calcularRuta(Request $request)
{
    $request->validate([
        'origen_id' => 'required|exists:lugares,id',
        'destino_id' => 'required|exists:lugares,id|different:origen_id'
    ]);

    \Log::info('=== INICIO CÁLCULO RUTA ===', [
        'origen_id' => $request->origen_id,
        'destino_id' => $request->destino_id,
        'timestamp' => now()
    ]);

    try {
        // 1. Construir el grafo
        \Log::info('Paso 1: Construyendo grafo...');
        $grafo = $this->construirGrafoDesdeBD();
        
        // Verificar grafo detalladamente
        \Log::info('Grafo construido - Resumen:', [
            'total_vertices' => $grafo->cantidadDeVertices(),
            'total_aristas' => $grafo->cantidadDeAristas()
        ]);
        
        // Listar todos los vértices
        $vertices = $grafo->getVertices();
        foreach ($vertices as $i => $vertice) {
            \Log::info("Vértice {$i}: ID={$vertice->id}, Nombre={$vertice->nombre}");
        }
        
        // 2. Buscar vértices específicos
        \Log::info('Paso 2: Buscando vértices origen y destino...');
        $origenObj = null;
        $destinoObj = null;
        
        foreach ($vertices as $vertice) {
            if ($vertice->id == $request->origen_id) {
                $origenObj = $vertice;
                \Log::info("✓ Vértice origen encontrado: ID={$vertice->id}, Nombre={$vertice->nombre}");
            }
            if ($vertice->id == $request->destino_id) {
                $destinoObj = $vertice;
                \Log::info("✓ Vértice destino encontrado: ID={$vertice->id}, Nombre={$vertice->nombre}");
            }
        }
        
        if (!$origenObj) {
            throw new \Exception("ERROR: No se encontró vértice origen con ID: {$request->origen_id}");
        }
        
        if (!$destinoObj) {
            throw new \Exception("ERROR: No se encontró vértice destino con ID: {$request->destino_id}");
        }
        
        // 3. Verificar adyacencias del origen
        \Log::info('Paso 3: Verificando adyacencias del origen...');
        $adyacentesOrigen = $grafo->getAdyacentesDeVertices($origenObj);
        \Log::info("El origen tiene " . count($adyacentesOrigen) . " vértices adyacentes:");
        
        foreach ($adyacentesOrigen as $adyacente) {
            $peso = $grafo->getPeso($origenObj, $adyacente);
            \Log::info("  → ID={$adyacente->id}, Nombre={$adyacente->nombre}, Peso={$peso}");
        }
        
        // 4. Dijkstra
        \Log::info('Paso 4: Ejecutando algoritmo Dijkstra...');
        $dijkstra = new Dijkstra($grafo, $origenObj);
        
        \Log::info('Calculando camino más corto...');
        $resultado = $dijkstra->getCaminoMasCorto($destinoObj);
        
        \Log::info('Resultado Dijkstra RAW:', [
            'costo' => $resultado['costo'],
            'tamaño_ruta' => count($resultado['ruta']),
            'es_infinito' => ($resultado['costo'] >= PHP_FLOAT_MAX - 1) ? 'Sí' : 'No'
        ]);
        
        // 5. Procesar resultado
        if ($resultado['costo'] < PHP_FLOAT_MAX - 1 && !empty($resultado['ruta'])) {
            \Log::info('✓ Ruta encontrada exitosamente!');
            
            $ruta = [
                'distancia_total' => round($resultado['costo'], 2),
                'camino' => $this->obtenerNombresLugares($resultado['ruta']),
                'camino_ids' => $this->obtenerIdsLugares($resultado['ruta']),
                'camino_objetos' => $resultado['ruta'], // Para debugging
                'error' => false,
                'usando_dijkstra' => true
            ];
            
            \Log::info('Ruta procesada:', [
                'distancia' => $ruta['distancia_total'],
                'pasos' => count($ruta['camino']),
                'camino' => implode(' → ', $ruta['camino'])
            ]);
            
        } else {
            \Log::warning('✗ Dijkstra no encontró ruta válida');
            
            // Verificar conectividad básica
            if (empty($adyacentesOrigen)) {
                \Log::error('El vértice origen NO tiene conexiones salientes!');
            }
            
            $ruta = [
                'distancia_total' => 0,
                'camino' => ['No se encontró ruta entre los lugares seleccionados'],
                'error' => true,
                'usando_dijkstra' => true,
                'mensaje' => 'El grafo no está conectado entre origen y destino'
            ];
        }

    } catch (\Exception $e) {
        \Log::error('❌ EXCEPCIÓN CAPTURADA:', [
            'mensaje' => $e->getMessage(),
            'archivo' => $e->getFile(),
            'línea' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // MOSTRAR ERROR REAL EN VEZ DE DATOS DE EJEMPLO
        $ruta = [
            'distancia_total' => 0,
            'camino' => ['Error en el cálculo: ' . $e->getMessage()],
            'error' => true,
            'usando_dijkstra' => false,
            'mensaje_error' => $e->getMessage()
        ];
    }

    \Log::info('=== FIN CÁLCULO RUTA ===');
    
    return redirect()->route('rutas.mostrar', [
        'origen' => $request->origen_id,
        'destino' => $request->destino_id
    ])->with('ruta', $ruta);
}

    public function mostrarRuta($origen, $destino)
    {
        $origenLugar = DB::table('lugares')->where('id', $origen)->first();
        $destinoLugar = DB::table('lugares')->where('id', $destino)->first();
        $ruta = session('ruta');

        return view('rutas.mostrar', compact('origenLugar', 'destinoLugar', 'ruta'));
    }

    private function construirGrafoDesdeBD()
{
    $grafo = new GrafoPesado();
    
    // 1. Obtener todos los lugares
    $lugaresData = DB::table('lugares')->orderBy('id')->get();
    \Log::info('Lugares en BD:', ['count' => $lugaresData->count()]);
    
    foreach ($lugaresData as $lugarData) {
        $lugar = new \stdClass();
        $lugar->id = $lugarData->id;
        $lugar->nombre = $lugarData->nombre;
        $lugar->x = $lugarData->x;
        $lugar->y = $lugarData->y;
        $lugar->categoria_id = $lugarData->categoria_id;
        
        try {
            $grafo->insertarVertice($lugar);
            \Log::debug('Vértice insertado:', ['id' => $lugar->id, 'nombre' => $lugar->nombre]);
        } catch (\Exception $e) {
            \Log::debug('Vértice ya existente:', ['id' => $lugar->id]);
        }
    }
    
    // 2. Agregar aristas (caminos)
    $caminos = DB::table('caminos')->get();
    \Log::info('Caminos en BD:', ['count' => $caminos->count()]);
    
    foreach ($caminos as $camino) {
        // Buscar los vértices por ID
        $origenObj = null;
        $destinoObj = null;
        
        foreach ($grafo->getVertices() as $vertice) {
            if ($vertice->id == $camino->lugar_origen_id) {
                $origenObj = $vertice;
            }
            if ($vertice->id == $camino->lugar_destino_id) {
                $destinoObj = $vertice;
            }
        }
        
        if ($origenObj && $destinoObj) {
            try {
                $grafo->insertarArista($origenObj, $destinoObj, (float)$camino->distancia);
                \Log::debug('Arista insertada:', [
                    'origen' => $origenObj->id,
                    'destino' => $destinoObj->id,
                    'distancia' => $camino->distancia
                ]);
                
                if ($camino->es_bidireccional) {
                    $grafo->insertarArista($destinoObj, $origenObj, (float)$camino->distancia);
                    \Log::debug('Arista inversa insertada:', [
                        'destino' => $destinoObj->id,
                        'origen' => $origenObj->id,
                        'distancia' => $camino->distancia
                    ]);
                }
            } catch (\Exception $e) {
                \Log::debug('Arista ya existente o error:', [
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            \Log::warning('No se encontraron vértices para camino:', [
                'origen_id' => $camino->lugar_origen_id,
                'destino_id' => $camino->lugar_destino_id
            ]);
        }
    }
    
    \Log::info('Grafo construido:', [
        'vertices' => $grafo->cantidadDeVertices(),
        'aristas' => $grafo->cantidadDeAristas()
    ]);
    
    return $grafo;
}

    private function buscarVerticePorId(GrafoPesado $grafo, $id)
    {
        $vertices = $grafo->getVertices();
        
        foreach ($vertices as $vertice) {
            if (is_object($vertice) && isset($vertice->id) && $vertice->id == $id) {
                return $vertice;
            }
        }
        
        return null;
    }

    private function obtenerNombresLugares(array $lugaresObjetos)
    {
        $nombres = [];
        
        foreach ($lugaresObjetos as $lugar) {
            if (is_object($lugar) && isset($lugar->nombre)) {
                $nombres[] = $lugar->nombre;
            } else {
                $nombres[] = "Lugar desconocido";
            }
        }
        
        return $nombres;
    }

    private function obtenerIdsLugares(array $lugaresObjetos)
    {
        $ids = [];
        
        foreach ($lugaresObjetos as $lugar) {
            if (is_object($lugar) && isset($lugar->id)) {
                $ids[] = $lugar->id;
            }
        }
        
        return $ids;
    }

    // Prueba simple para verificar datos
public function testDatos()
{
    \Log::info('=== TEST DE DATOS ===');
    
    // 1. Verificar lugares
    $lugares = DB::table('lugares')->get();
    \Log::info('Total lugares en DB: ' . $lugares->count());
    
    foreach ($lugares as $lugar) {
        \Log::info("Lugar ID: {$lugar->id}, Nombre: {$lugar->nombre}");
    }
    
    // 2. Verificar caminos
    $caminos = DB::table('caminos')->get();
    \Log::info('Total caminos en DB: ' . $caminos->count());
    
    foreach ($caminos as $camino) {
        \Log::info("Camino: {$camino->lugar_origen_id} → {$camino->lugar_destino_id}, Distancia: {$camino->distancia}, Bidireccional: {$camino->es_bidireccional}");
    }
    
    // 3. Verificar conexiones específicas
    $origenId = 1; // Cambia por un ID real
    $destinoId = 236; // Cambia por un ID real
    
    $caminosEntre = DB::table('caminos')
        ->where(function($query) use ($origenId, $destinoId) {
            $query->where('lugar_origen_id', $origenId)
                  ->where('lugar_destino_id', $destinoId);
        })
        ->orWhere(function($query) use ($origenId, $destinoId) {
            $query->where('lugar_origen_id', $destinoId)
                  ->where('lugar_destino_id', $origenId);
        })
        ->get();
    
    \Log::info("Caminos directos entre {$origenId} y {$destinoId}: " . $caminosEntre->count());
}
}