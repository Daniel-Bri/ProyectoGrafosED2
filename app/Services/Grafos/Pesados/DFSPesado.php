<?php

namespace App\Services\Grafos\Pesados;

use App\Services\Grafos\Pesados\RecorridoGrafoPesado;
use SplStack; // Alternativa más eficiente si usas extensiones SPL

class DFSPesado extends RecorridoGrafoPesado
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
        $posDeVertice = $this->elGrafo->getPosicionDeVertice($verticeEnTurno);

        // Usamos un array como pila (Stack) - [array_pop] es el equivalente a [pop]
        /** @var array<int> $pilaDeVertices */
        $pilaDeVertices = [];
        $pilaDeVertices[] = $posDeVertice; // push

        $this->controlMarcados->marcarVertice($posDeVertice);

        while (!empty($pilaDeVertices)) {
            $posDeVerticeAProcesar = array_pop($pilaDeVertices); // pop

            // El código Java tiene un error: debe agregar el vértice PROCESADO (posDeVerticeAProcesar) al recorrido
            // recorrido.add(elGrafo.getVerticePorPosicion(posDeVertice)); <-- En Java dice $posDeVertice, debería ser $posDeVerticeAProcesar
            $verticeAProcesar = $this->elGrafo->getVerticePorPosicion($posDeVerticeAProcesar);
            $this->recorrido[] = $verticeAProcesar;

            // En el código Java, se usa $verticeEnTurno para obtener adyacentes,
            // pero DEBERÍA ser el que se acaba de sacar de la pila.
            // Iterable<T> adyacentesDelVertice = elGrafo.getAdyacentesDeVertices(verticeEnTurno); <-- ERROR, debería ser verticeAProcesar
            $adyacentesDelVertice = $this->elGrafo->getAdyacentesDeVertices($verticeAProcesar);

            // Iterar en orden inverso para que el DFS visite primero el adyacente más pequeño
            // (el que es comparativamente menor) al hacer pop. Esto simula el Collections.sort
            // y la estructura de lista de adyacencia ordenada.
            $adyacentesInverso = array_reverse(iterator_to_array($adyacentesDelVertice));
            foreach ($adyacentesInverso as $adyacente) {
                $posDeAdyacente = $this->elGrafo->getPosicionDeVertice($adyacente);
                if (!$this->controlMarcados->estaVerticeMarcado($posDeAdyacente)) {
                    $pilaDeVertices[] = $posDeAdyacente; // push
                    $this->controlMarcados->marcarVertice($posDeAdyacente);
                }
            }
        }
    }
}