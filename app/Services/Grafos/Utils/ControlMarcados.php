<?php

namespace App\Services\Grafos\Utils;

class ControlMarcados
{
    /** @var array<bool> */
    private $marcados;

    public function __construct(int $nroDeVertices)
    {
        if ($nroDeVertices < 0) {
            throw new \InvalidArgumentException("Cantidad de vertices invalidos");
        }
        
        // Inicializa el array con 'false' para todos los vértices.
        $this->marcados = array_fill(0, $nroDeVertices, false);
    }

    /**
     * Marca un vértice como visitado.
     * @param int $posDeVertice
     */
    public function marcarVertice(int $posDeVertice): void
    {
        // En PHP, simplemente asignamos el valor booleano
        $this->marcados[$posDeVertice] = true;
    }

    /**
     * Desmarca un vértice.
     * @param int $posDeVertice
     */
    public function desmarcarVertice(int $posDeVertice): void
    {
        $this->marcados[$posDeVertice] = false;
    }

    /**
     * Verifica si un vértice está marcado.
     * @param int $posDeVertice
     * @return bool
     */
    public function estaVerticeMarcado(int $posDeVertice): bool
    {
        // Asegúrate de que la posición exista antes de acceder, aunque en teoría debería
        // estar validada por la inicialización correcta.
        if (!isset($this->marcados[$posDeVertice])) {
             throw new \OutOfBoundsException("Posición de vértice fuera de rango.");
        }
        return $this->marcados[$posDeVertice];
    }

    /**
     * Desmarca todos los vértices.
     */
    public function desmarcarTodosLosVertices(): void
    {
        $nroDeVertices = count($this->marcados);
        $this->marcados = array_fill(0, $nroDeVertices, false);
    }

    /**
     * Verifica si todos los vértices han sido marcados.
     * @return bool
     */
    public function estanTodosLosVerticesMarcados(): bool
    {
        // array_search busca la primera ocurrencia de FALSE.
        // Si no se encuentra (devuelve false), significa que todos son TRUE.
        return array_search(false, $this->marcados, true) === false;
    }
}