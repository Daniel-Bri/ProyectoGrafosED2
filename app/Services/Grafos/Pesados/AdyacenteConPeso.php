<?php

namespace App\Services\Grafos\Pesados;

// No implementaremos una interfaz Comparable como tal, sino un método
// compareTo para usar usort si es necesario, y nos enfocaremos en la
// lógica de equals/hashCode para la búsqueda/eliminación.

class AdyacenteConPeso
{
    /** @var int */
    private $indiceVertice;
    /** @var float */
    private $peso;

    public function __construct(int $vertice, ?float $peso = null)
    {
        $this->indiceVertice = $vertice;
        $this->peso = $peso ?? 0.0; // Valor por defecto si no se pasa peso
    }

    public function getIndiceVertice(): int
    {
        return $this->indiceVertice;
    }

    public function setIndiceVertice(int $vertice): void
    {
        $this->indiceVertice = $vertice;
    }

    public function getPeso(): float
    {
        return $this->peso;
    }

    public function setPeso(float $peso): void
    {
        $this->peso = $peso;
    }

    /**
     * Compara este objeto con otro AdyacenteConPeso basado en el índice del vértice.
     * Reemplaza el compareTo de Java para Collections.sort.
     * @param AdyacenteConPeso $otro
     * @return int -1 si es menor, 0 si es igual, 1 si es mayor.
     */
    public function compareTo(AdyacenteConPeso $otro): int
    {
        return $this->indiceVertice <=> $otro->indiceVertice;
    }

    /**
     * Determina si dos objetos AdyacenteConPeso son iguales, basado solo en el índice del vértice.
     * Reemplaza el equals de Java.
     * @param mixed $otro
     * @return bool
     */
    public function equals($otro): bool
    {
        if (!$otro instanceof AdyacenteConPeso) {
            return false;
        }

        // El peso no es relevante para la igualdad, solo el índice.
        return $this->indiceVertice === $otro->indiceVertice;
    }

    // En PHP, para eliminar un objeto de un array, usaremos la función equals
    // en un bucle o una función de búsqueda personalizada, ya que array_search
    // y array_keys solo funcionan para tipos escalares o requieren serialización.
}