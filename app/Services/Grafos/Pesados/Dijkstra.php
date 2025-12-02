<?php

namespace App\Services\Grafos\Pesados;

use SplPriorityQueue; // Necesario para la cola de prioridad

class Dijkstra
{
    /** @var GrafoPesado */
    protected $grafo;
    /** @var array<mixed, float> */
    protected $distancias;
    /** @var array<mixed, mixed|null> */
    protected $predecesores;
    /** @var mixed */
    protected $verticeInicial;
    
    // Necesitamos una constante que represente el infinito
    protected const INFINITO = PHP_FLOAT_MAX;

    /**
     * @param GrafoPesado $grafo
     * @param mixed $verticeInicial
     */
    public function __construct(GrafoPesado $grafo, $verticeInicial)
    {
        $this->grafo = $grafo;
        $this->verticeInicial = $verticeInicial;
        $this->distancias = [];
        $this->predecesores = [];
        $this->ejecutarAlgoritmoDijkstra();
    }

    protected function ejecutarAlgoritmoDijkstra(): void
    {
        $this->grafo->validarVertice($this->verticeInicial);
        $vertices = $this->grafo->getVertices();
        
        // 1. Inicializar distancias y predecesores
        foreach ($vertices as $vertice) {
            // Usamos el objeto Lugar (vértice) como clave en el array, si es un objeto con ID, 
            // sino, podríamos usar el ID del lugar para la clave del array.
            // Para simplificar, usaremos el ID del lugar (asumiendo que $vertice es un objeto Lugar con ID)
            $idVertice = $vertice->id; 
            
            $this->distancias[$idVertice] = self::INFINITO;
            $this->predecesores[$idVertice] = null;
        }

        $idInicial = $this->verticeInicial->id;
        $this->distancias[$idInicial] = 0.0;
        
        // 2. Usar cola de prioridad (Priority Queue)
        $colaPrioridad = new SplPriorityQueue();
        
        // La cola de prioridad de PHP es "max-heap" por defecto (los más grandes tienen mayor prioridad).
        // Dijkstra necesita "min-heap" (los más pequeños tienen mayor prioridad). 
        // Usamos la distancia negativa como prioridad para simular min-heap.
        // El formato de SplPriorityQueue es insert($valor, $prioridad)
        $colaPrioridad->insert($this->verticeInicial, 0.0); // Prioridad: -0.0

        while (!$colaPrioridad->isEmpty()) {
            /** @var Lugar $verticeActual */
            $verticeActual = $colaPrioridad->extract();
            $idActual = $verticeActual->id;

            // En SplPriorityQueue, el valor que se extrae no tiene el costo asociado.
            // Verificamos si ya encontramos una ruta más corta (necesario por el funcionamiento de la cola de PHP)
            if ($this->distancias[$idActual] < $colaPrioridad->current()) {
                 continue; // Ya procesamos este vértice con un costo menor
            }

            $adyacentes = $this->grafo->getAdyacentesDeVertices($verticeActual);

            foreach ($adyacentes as $adyacente) {
                $idAdyacente = $adyacente->id;
                $peso = $this->grafo->getPeso($verticeActual, $adyacente);
                
                $nuevaDistancia = $this->distancias[$idActual] + $peso;

                if ($nuevaDistancia < $this->distancias[$idAdyacente]) {
                    // Relajación: hemos encontrado un camino más corto
                    $this->distancias[$idAdyacente] = $nuevaDistancia;
                    $this->predecesores[$idAdyacente] = $verticeActual;
                    
                    // Reinsertar en la cola con la nueva (menor) prioridad (distancia)
                    // Usamos la distancia negativa para simular min-heap
                    $colaPrioridad->insert($adyacente, -$nuevaDistancia); 
                }
            }
        }
    }

    /**
     * Reconstruye el camino más corto al destino.
     * @param mixed $verticeDestino
     * @return array{'ruta': array<mixed>, 'costo': float}
     */
    public function getCaminoMasCorto($verticeDestino): array
    {
        $this->grafo->validarVertice($verticeDestino);
        $idDestino = $verticeDestino->id;
        
        $ruta = [];
        $costo = $this->distancias[$idDestino] ?? self::INFINITO;

        if ($costo === self::INFINITO) {
            return ['ruta' => [], 'costo' => $costo];
        }

        $vertice = $verticeDestino;
        while ($vertice !== null) {
            // Agregar al inicio de la ruta
            array_unshift($ruta, $vertice);
            
            // Obtener el predecesor (por ID)
            $vertice = $this->predecesores[$vertice->id] ?? null;
            
            // Si el predecesor es el inicial, se detiene después de agregarlo
            if ($vertice === $this->verticeInicial) {
                array_unshift($ruta, $this->verticeInicial);
                break; 
            }
        }
        
        // La ruta debe comenzar con el verticeInicial, si no es así, algo salió mal
        if (empty($ruta) || $ruta[0]->id !== $this->verticeInicial->id) {
            return ['ruta' => [], 'costo' => self::INFINITO];
        }

        return ['ruta' => $ruta, 'costo' => $costo];
    }
}