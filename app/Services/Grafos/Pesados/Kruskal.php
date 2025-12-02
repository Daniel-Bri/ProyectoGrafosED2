<?php

namespace App\Services\Grafos\Pesados;

use App\Services\Grafos\Utils\ControlMarcados;
use App\Services\Grafos\Excepciones\ExcepcionAristaYaExiste;
use App\Services\Grafos\Excepciones\ExcepcionAristaNoExiste;

class Kruskal
{
    /** @var array<Arista> */
    protected $listaDeKruskal;
    /** @var GrafoPesado */
    protected $grafo;
    /** @var GrafoPesado */
    protected $grafoAux;
    /** @var ControlMarcados */
    protected $controlMarcados;

    /**
     * @param GrafoPesado $unGrafo
     */
    public function __construct(GrafoPesado $unGrafo)
    {
        $this->grafo = $unGrafo;
        $this->grafoAux = new GrafoPesado();
        $this->listaDeKruskal = [];
        $this->controlMarcados = new ControlMarcados($unGrafo->cantidadDeVertices());

        // 1. Recoger todas las aristas y guardarlas en listaDeKruskal
        for ($i = 0; $i < $this->grafo->cantidadDeVertices(); $i++) {
            $verticeOrigen = $this->grafo->getVerticePorPosicion($i);
            $adyacentes = $this->grafo->getAdyacentesDeVertices($verticeOrigen);

            $this->controlMarcados->marcarVertice($i); // Marcar origen para evitar duplicados (A-B, B-A)

            foreach ($adyacentes as $adyacente) {
                $posDeAdyacente = $this->grafo->getPosicionDeVertice($adyacente);
                if (!$this->controlMarcados->estaVerticeMarcado($posDeAdyacente)) {
                    $peso = $this->grafo->getPeso($verticeOrigen, $adyacente);
                    $arista = new Arista($i, $posDeAdyacente, $peso);
                    $this->listaDeKruskal[] = $arista;
                }
            }
        }
        $this->controlMarcados->desmarcarTodos();

        // 2. Ordenar todas las aristas por peso
        usort($this->listaDeKruskal, function (Arista $a, Arista $b) {
            return $a->compareTo($b);
        });

        // 3. Insertar todos los vértices en el grafo auxiliar
        $vertices = $this->grafo->getVertices();
        foreach ($vertices as $vertice) {
            $this->grafoAux->insertarVertice($vertice);
        }

        // 4. Ejecutar el algoritmo
        $this->ejecutarAlgoritmoKruskal();
    }

    public function ejecutarAlgoritmoKruskal(): void
    {
        foreach ($this->listaDeKruskal as $arista) {
            $origen = $arista->getOrigen();
            $destino = $arista->getDestino();
            $peso = $arista->getPeso();

            $verticeOrigen = $this->grafo->getVerticePorPosicion($origen);
            $verticeDestino = $this->grafo->getVerticePorPosicion($destino);

            try {
                // Intentar insertar la arista
                $this->grafoAux->insertarArista($verticeOrigen, $verticeDestino, $peso);

                // Si se insertó y causa un ciclo, la eliminamos
                if ($this->grafoAux->hayCiclos()) {
                    $this->grafoAux->eliminarArista($verticeOrigen, $verticeDestino);
                }
            } catch (ExcepcionAristaYaExiste $e) {
                // No debería ocurrir si el algoritmo se ejecuta bien
            } catch (ExcepcionAristaNoExiste $e) {
                // No debería ocurrir si se acaba de insertar
            }
        }
    }

    /**
     * @return array<Arista>
     */
    public function getListaDeKruskal(): array
    {
        return $this->listaDeKruskal;
    }

    /**
     * @return GrafoPesado
     */
    public function getArbolDeCostoMinimo(): GrafoPesado
    {
        return $this->grafoAux;
    }
}