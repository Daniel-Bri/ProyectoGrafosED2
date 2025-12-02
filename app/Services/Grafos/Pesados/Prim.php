<?php

namespace App\Services\Grafos\Pesados;

use SplPriorityQueue;
use App\Services\Grafos\Utils\ControlMarcados;

class Prim
{
    /** @var GrafoPesado */
    protected $grafo;
    /** @var GrafoPesado */
    protected $mst; // Árbol de expansión mínima resultante
    
    protected const INFINITO = PHP_FLOAT_MAX;

    /**
     * @param GrafoPesado $grafo Debe ser conexo y no dirigido.
     * @param mixed $verticeInicial
     */
    public function __construct(GrafoPesado $grafo, $verticeInicial)
    {
        $this->grafo = $grafo;
        $this->mst = new GrafoPesado();
        $this->verticeInicial = $verticeInicial;
        
        // Copiar vértices al MST
        foreach ($this->grafo->getVertices() as $vertice) {
            $this->mst->insertarVertice($vertice);
        }
        
        $this->ejecutarAlgoritmoPrim();
    }

    protected function ejecutarAlgoritmoPrim(): void
    {
        $this->grafo->validarVertice($this->verticeInicial);
        $controlMarcados = new ControlMarcados($this->grafo->cantidadDeVertices());
        
        // Usamos una cola de prioridad para Aristas (o Adyacentes con su peso)
        $colaPrioridad = new SplPriorityQueue();
        
        // Necesitamos una estructura para almacenar la arista que conecta a cada vértice con el MST
        // Clave: ID de vértice | Valor: Arista (Arista.php)
        $aristasMinimas = []; 

        // Inicializar
        $vertices = $this->grafo->getVertices();
        $idInicial = $this->verticeInicial->id;
        
        // Inicializamos las prioridades de todas las aristas (usando el peso negativo para min-heap)
        $this->agregarAristasAdyacentes($this->verticeInicial, $colaPrioridad, $controlMarcados);
        $controlMarcados->marcarVertice($this->grafo->getPosicionDeVertice($this->verticeInicial));
        
        $aristasAgregadas = 0;
        $numVertices = $this->grafo->cantidadDeVertices();

        // El MST tendrá (V - 1) aristas
        while ($aristasAgregadas < ($numVertices - 1) && !$colaPrioridad->isEmpty()) {
            
            // Extraer la arista con el menor peso (mayor prioridad negativa)
            /** @var Arista $aristaMinima */
            $aristaMinima = $colaPrioridad->extract();
            
            // La prioridad es el peso negativo, lo ignoramos al extraer.
            
            $posDestino = $aristaMinima->getDestino();
            $verticeDestino = $this->grafo->getVerticePorPosicion($posDestino);
            
            if (!$controlMarcados->estaVerticeMarcado($posDestino)) {
                
                // 1. Agregar el vértice al MST
                $controlMarcados->marcarVertice($posDestino);
                
                // 2. Agregar la arista al MST
                $posOrigen = $aristaMinima->getOrigen();
                $verticeOrigen = $this->grafo->getVerticePorPosicion($posOrigen);
                
                // La inserción bidireccional se hace en GrafoPesado
                $this->mst->insertarArista($verticeOrigen, $verticeDestino, $aristaMinima->getPeso());
                $aristasAgregadas++;
                
                // 3. Agregar todas las aristas salientes del nuevo vértice (destino) a la cola
                $this->agregarAristasAdyacentes($verticeDestino, $colaPrioridad, $controlMarcados);
            }
        }
    }

    /**
     * Agrega aristas de un vértice a la cola si el destino no está marcado.
     * @param mixed $vertice
     * @param SplPriorityQueue $cola
     * @param ControlMarcados $marcados
     */
    protected function agregarAristasAdyacentes($vertice, SplPriorityQueue $cola, ControlMarcados $marcados): void
    {
        $posOrigen = $this->grafo->getPosicionDeVertice($vertice);
        $adyacentes = $this->grafo->getAdyacentesDeVertices($vertice);
        
        foreach ($adyacentes as $adyacente) {
            $posAdyacente = $this->grafo->getPosicionDeVertice($adyacente);
            
            if (!$marcados->estaVerticeMarcado($posAdyacente)) {
                $peso = $this->grafo->getPeso($vertice, $adyacente);
                
                // Creamos un objeto Arista (posOrigen, posAdyacente, peso)
                $arista = new Arista($posOrigen, $posAdyacente, $peso);
                
                // Insertamos el objeto Arista en la cola, usando el peso negativo como prioridad
                $cola->insert($arista, -$peso); 
            }
        }
    }

    /**
     * @return GrafoPesado
     */
    public function getArbolDeCostoMinimo(): GrafoPesado
    {
        // Se podría agregar una validación si el grafo no era conexo y el MST no se completó
        return $this->mst;
    }
}