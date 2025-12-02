<?php

namespace App\Services\Grafos\Pesados;

class Arista
{
    /** @var int */
    private $origen;
    /** @var int */
    private $destino;
    /** @var float */
    private $peso;

    public function __construct(int $origen, int $destino, float $peso)
    {
        $this->origen = $origen;
        $this->destino = $destino;
        $this->peso = $peso;
    }

    /**
     * Compara esta arista con otra basada en el peso.
     * Reemplaza el compareTo de Java para Collections.sort.
     * @param Arista $otra
     * @return int -1 si es menor, 0 si es igual, 1 si es mayor.
     */
    public function compareTo(Arista $otra): int
    {
        return $this->peso <=> $otra->peso;
    }

    public function getOrigen(): int
    {
        return $this->origen;
    }

    public function getDestino(): int
    {
        return $this->destino;
    }

    public function getPeso(): float
    {
        return $this->peso;
    }
}