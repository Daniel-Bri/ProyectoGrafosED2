<?php

namespace App\Services\Grafos\Pesados;

use App\Services\Grafos\Pesados\RecorridoGrafoPesado;
use SplQueue; // Alternativa más eficiente si usas extensiones SPL

class BFSPesado extends RecorridoGrafoPesado
{
    /**
     * @param GrafoPesado $unGrafo
     * @param mixed $verticeInicial
     */
    public function __construct(GrafoPesado $unGrafo, $verticeInicial)
    {
        parent::__construct($unGrafo, $verticeInicial);
    }

    /**
     * @param mixed $verticeEnTurno
     * @return void
     */
    public function ejecutarRecorrido($verticeEnTurno): void
    {
        $this->elGrafo->validarVertice($verticeEnTurno);
        $posDelVerticeEnTurno = $this->elGrafo->getPosicionDeVertice($verticeEnTurno);

        // Usamos un array como cola (Queue) - [array_shift] es el equivalente a [poll]
        /** @var array<int> $colaDeVertices */
        $colaDeVertices = [];
        $colaDeVertices[] = $posDelVerticeEnTurno; // add

        $this->controlMarcados->marcarVertice($posDelVerticeEnTurno);

        while (!empty($colaDeVertices)) {
            $posDeVerticeAProcesar = array_shift($colaDeVertices); // poll

            $this->recorrido[] = $this->elGrafo->getVerticePorPosicion($posDeVerticeAProcesar);

            // En el código Java, se usa $verticeEnTurno para obtener adyacentes,
            // pero DEBERÍA ser el que se acaba de sacar de la cola.
            // A diferencia de tu Java:
            // Iterable<T> adyacentesDelVertice = elGrafo.getAdyacentesDeVertices(verticeEnTurno); <-- ERROR, debería ser verticeAProcesar
            $verticeAProcesar = $this->elGrafo->getVerticePorPosicion($posDeVerticeAProcesar);
            $adyacentesDelVertice = $this->elGrafo->getAdyacentesDeVertices($verticeAProcesar);

            foreach ($adyacentesDelVertice as $adyacente) {
                $posDelAdyacente = $this->elGrafo->getPosicionDeVertice($adyacente);
                if (!$this->controlMarcados->estaVerticeMarcado($posDelAdyacente)) {
                    $colaDeVertices[] = $posDelAdyacente; // add
                    $this->controlMarcados->marcarVertice($posDelAdyacente);
                }
            }
        }
    }
}