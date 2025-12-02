<?php

namespace App\Services\Grafos\Pesados;

use SplPriorityQueue;

class Dijkstra
{
    /** @var GrafoPesado */
    protected $grafo;
    /** @var array<int, float> */
    protected $distancias;
    /** @var array<int, int|null> */  // Cambiado: almacena POSICIONES, no objetos
    protected $predecesores;
    /** @var int */
    protected $posInicial;
    /** @var mixed */
    protected $verticeInicial;
    
    protected const INFINITO = PHP_FLOAT_MAX;

    public function __construct(GrafoPesado $grafo, $verticeInicial)
    {
        $this->grafo = $grafo;
        $this->verticeInicial = $verticeInicial;
        $this->posInicial = $grafo->getPosicionDeVertice($verticeInicial);
        $this->distancias = [];
        $this->predecesores = [];
        $this->ejecutarAlgoritmoDijkstra();
    }

    protected function ejecutarAlgoritmoDijkstra(): void
    {
        $n = $this->grafo->cantidadDeVertices();
        
        // 1. Inicializar distancias y predecesores con POSICIONES
        for ($i = 0; $i < $n; $i++) {
            $this->distancias[$i] = self::INFINITO;
            $this->predecesores[$i] = null; // null significa sin predecesor
        }

        $this->distancias[$this->posInicial] = 0.0;
        
        // 2. Usar cola de prioridad
        $colaPrioridad = new SplPriorityQueue();
        $colaPrioridad->setExtractFlags(SplPriorityQueue::EXTR_DATA);
        
        // Insertar POSICIÓN del vértice inicial
        $colaPrioridad->insert($this->posInicial, 0.0);

        while (!$colaPrioridad->isEmpty()) {
            $posActual = $colaPrioridad->extract();
            
            // Obtener el vértice actual desde su posición
            $verticeActual = $this->grafo->getVerticePorPosicion($posActual);
            $adyacentes = $this->grafo->getAdyacentesDeVertices($verticeActual);

            foreach ($adyacentes as $adyacente) {
                $posAdyacente = $this->grafo->getPosicionDeVertice($adyacente);
                $peso = $this->grafo->getPeso($verticeActual, $adyacente);
                
                $nuevaDistancia = $this->distancias[$posActual] + $peso;

                if ($nuevaDistancia < $this->distancias[$posAdyacente]) {
                    // Relajación - almacenar POSICIÓN del predecesor
                    $this->distancias[$posAdyacente] = $nuevaDistancia;
                    $this->predecesores[$posAdyacente] = $posActual; // Almacenar posición, no objeto
                    
                    // Insertar POSICIÓN en la cola
                    $colaPrioridad->insert($posAdyacente, -$nuevaDistancia);
                }
            }
        }
    }

    public function getCaminoMasCorto($verticeDestino): array
    {
        $posDestino = $this->grafo->getPosicionDeVertice($verticeDestino);
        $costo = $this->distancias[$posDestino] ?? self::INFINITO;

        if ($costo >= self::INFINITO - 1) {
            return ['ruta' => [], 'costo' => $costo];
        }

        // Reconstruir ruta usando POSICIONES
        $rutaPosiciones = [];
        $posActual = $posDestino;
        
        while ($posActual !== null) {
            array_unshift($rutaPosiciones, $posActual);
            $posActual = $this->predecesores[$posActual];
            
            // Si llegamos al inicio, salir
            if ($posActual === $this->posInicial) {
                array_unshift($rutaPosiciones, $posActual);
                break;
            }
        }
        
        // Asegurar que el inicio esté incluido
        if (empty($rutaPosiciones) || $rutaPosiciones[0] !== $this->posInicial) {
            array_unshift($rutaPosiciones, $this->posInicial);
        }

        // Convertir posiciones a vértices
        $ruta = [];
        foreach ($rutaPosiciones as $pos) {
            $ruta[] = $this->grafo->getVerticePorPosicion($pos);
        }

        return ['ruta' => $ruta, 'costo' => $costo];
    }

    /**
     * Obtiene la distancia más corta a un vértice
     */
    public function getDistancia($vertice): float
    {
        $pos = $this->grafo->getPosicionDeVertice($vertice);
        return $this->distancias[$pos] ?? self::INFINITO;
    }
}