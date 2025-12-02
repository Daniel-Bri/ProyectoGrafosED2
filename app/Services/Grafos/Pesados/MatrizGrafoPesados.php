<?php

namespace App\Services\Grafos\Pesados;

class MatrizGrafoPesados
{
    /** @var GrafoPesado */
    protected $grafo;
    /** @var array<array<float>> */
    protected $matrizDePesos;
    /** @var int */
    protected $n;

    /**
     * @param GrafoPesado $unGrafo
     */
    public function __construct(GrafoPesado $unGrafo)
    {
        $this->grafo = $unGrafo;
        $this->n = $unGrafo->cantidadDeVertices();
        $this->matrizDePesos = [];

        for ($i = 0; $i < $this->n; $i++) {
            $this->matrizDePesos[$i] = array_fill(0, $this->n, 0.0);
        }

        for ($i = 0; $i < $this->n; $i++) {
            $verticeOrigen = $unGrafo->getVerticePorPosicion($i);
            $adyacentes = $unGrafo->getAdyacentesDeVertices($verticeOrigen);

            foreach ($adyacentes as $adyacente) {
                $posDeAdyacente = $unGrafo->getPosicionDeVertice($adyacente);
                $peso = $unGrafo->getPeso($verticeOrigen, $adyacente);
                $this->matrizDePesos[$i][$posDeAdyacente] = $peso;
            }
        }
    }

    /**
     * @return array<array<float>>
     */
    public function getMatrizDePesos(): array
    {
        return $this->matrizDePesos;
    }

    /**
     * Implementa el algoritmo de Warshall para determinar conectividad.
     * Retorna una matriz de 0s y 1s.
     * @return array<array<float>>
     */
    public function getMatrizDeWarshall(): array
    {
        // Clonar la matriz de pesos, pero solo el estado inicial 0/peso
        // para transformarla en una matriz de adyacencia (0 o 1) para Warshall
        /** @var array<array<float>> $matrizDeWarshall */
        $matrizDeWarshall = [];
        for ($i = 0; $i < $this->n; $i++) {
            $matrizDeWarshall[$i] = array_fill(0, $this->n, 0.0);
            for ($j = 0; $j < $this->n; $j++) {
                if ($this->matrizDePesos[$i][$j] !== 0.0) {
                    $matrizDeWarshall[$i][$j] = 1.0;
                }
            }
        }

        $n = $this->n;
        for ($k = 0; $k < $n; $k++) {
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    // Si ya hay camino O (hay camino i->k Y camino k->j)
                    if ($matrizDeWarshall[$i][$j] !== 0.0 || ($matrizDeWarshall[$i][$k] !== 0.0 && $matrizDeWarshall[$k][$j] !== 0.0)) {
                        $matrizDeWarshall[$i][$j] = 1.0;
                    }
                }
            }
        }
        return $matrizDeWarshall;
    }
}