<?php

namespace App\Services\Grafos\Pesados;

use App\Services\Grafos\Excepciones\ExcepcionAristaNoExiste;
use App\Services\Grafos\Excepciones\ExcepcionAristaYaExiste;
use App\Services\Grafos\Utils\ControlMarcados;

class GrafoPesado
{
    /** @var array<mixed> */
    protected $listaDeVertices;
    /** @var array<array<AdyacenteConPeso>> */
    protected $listaDeAdyacencias;

    protected const POS_DE_VERTICE_INVALIDO = -1;

    public function __construct(?iterable $vertices = null)
    {
        $this->listaDeVertices = [];
        $this->listaDeAdyacencias = [];

        if ($vertices !== null) {
            foreach ($vertices as $unVertice) {
                $this->insertarVertice($unVertice);
            }
        }
    }

    /**
     * @param mixed $unVertice
     * @return int
     */
    public function getPosicionDeVertice($unVertice): int
    {
        // En PHP, para tipos genéricos (comparable), la forma más simple es buscar
        foreach ($this->listaDeVertices as $i => $verticeEnTurno) {
            // Asumiendo que el tipo T implementa un método de comparación o se puede comparar directamente.
            // En Java usa compareTo, que para tipos escalares en PHP es <=>
            if ($verticeEnTurno === $unVertice) { // Comparación estricta para simplificar (o usar un método compareTo si es un objeto)
                return $i;
            }
        }
        return self::POS_DE_VERTICE_INVALIDO;
    }

    /**
     * @param mixed $unVertice
     * @return bool
     */
    public function existeVertice($unVertice): bool
    {
        return $this->getPosicionDeVertice($unVertice) !== self::POS_DE_VERTICE_INVALIDO;
    }

    /**
     * @param mixed $unVertice
     * @return void
     */
    public function validarVertice($unVertice): void
    {
        if ($this->getPosicionDeVertice($unVertice) === self::POS_DE_VERTICE_INVALIDO) {
            throw new \InvalidArgumentException("El vertice " . (string)$unVertice . " no existe en su grafo");
        }
    }

    public function cantidadDeVertices(): int
    {
        return count($this->listaDeVertices);
    }

    /**
     * @param mixed $unVertice
     * @return void
     */
    public function insertarVertice($unVertice): void
    {
        if ($this->existeVertice($unVertice)) {
            throw new \InvalidArgumentException("El vertice " . (string)$unVertice . " ya existe en su grafo");
        }
        $this->listaDeVertices[] = $unVertice;
        $this->listaDeAdyacencias[] = []; // Nueva lista de adyacencias
    }

    /**
     * @return array<mixed>
     */
    public function getVertices(): array
    {
        return $this->listaDeVertices;
    }

    /**
     * @param mixed $unVertice
     * @return array<mixed>
     */
    public function getAdyacentesDeVertices($unVertice): array
    {
        $this->validarVertice($unVertice);
        $posDelVertice = $this->getPosicionDeVertice($unVertice);
        /** @var array<AdyacenteConPeso> $adyacentesDelVertice */
        $adyacentesDelVertice = $this->listaDeAdyacencias[$posDelVertice];

        $listaDeVerticesAdyacentes = [];
        foreach ($adyacentesDelVertice as $adyacenteConPeso) {
            $listaDeVerticesAdyacentes[] = $this->listaDeVertices[$adyacenteConPeso->getIndiceVertice()];
        }
        return $listaDeVerticesAdyacentes;
    }

    /**
     * @param mixed $verticeOrigen
     * @param mixed $verticeDestino
     * @return bool
     */
    public function existeAdyacencia($verticeOrigen, $verticeDestino): bool
    {
        $this->validarVertice($verticeOrigen);
        if (!$this->existeVertice($verticeDestino)) {
            return false;
        }

        $posDelVerticeOrigen = $this->getPosicionDeVertice($verticeOrigen);
        $posDelVerticeDestino = $this->getPosicionDeVertice($verticeDestino);

        /** @var array<AdyacenteConPeso> $adyacentesDelOrigen */
        $adyacentesDelOrigen = $this->listaDeAdyacencias[$posDelVerticeOrigen];

        // Se necesita un objeto AdyacenteConPeso para usar el método equals para la búsqueda
        $adyacenciaDestino = new AdyacenteConPeso($posDelVerticeDestino);

        foreach ($adyacentesDelOrigen as $adyacente) {
            if ($adyacente->equals($adyacenciaDestino)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $unVertice
     * @return int
     */
    public function gradoDelVertice($unVertice): int
    {
        $this->validarVertice($unVertice);
        $posDelVertice = $this->getPosicionDeVertice($unVertice);
        return count($this->listaDeAdyacencias[$posDelVertice]);
    }

    /**
     * @param int $posDeVertice
     * @return mixed
     */
    public function getVerticePorPosicion(int $posDeVertice)
    {
        if ($posDeVertice < 0 || $posDeVertice >= count($this->listaDeVertices)) {
            throw new \InvalidArgumentException("La posicion del vertice no es valida");
        }
        return $this->listaDeVertices[$posDeVertice];
    }

    /**
     * @param mixed $verticeOrigen
     * @param mixed $verticeDestino
     * @param float $peso
     * @return void
     * @throws ExcepcionAristaYaExiste
     */
    public function insertarArista($verticeOrigen, $verticeDestino, float $peso): void
    {
        if ($this->existeAdyacencia($verticeOrigen, $verticeDestino)) {
            throw new ExcepcionAristaYaExiste();
        }

        $posDeVerticeOrigen = $this->getPosicionDeVertice($verticeOrigen);
        $posDeVerticeDestino = $this->getPosicionDeVertice($verticeDestino);

        /** @var array<AdyacenteConPeso> $adyacentesDelOrigen */
        $adyacentesDelOrigen = &$this->listaDeAdyacencias[$posDeVerticeOrigen];
        $adyacentesDelOrigen[] = new AdyacenteConPeso($posDeVerticeDestino, $peso);

        // PHP usort para ordenar la lista de adyacencias, usando el compareTo
        usort($adyacentesDelOrigen, function (AdyacenteConPeso $a, AdyacenteConPeso $b) {
            return $a->compareTo($b);
        });


        if ($posDeVerticeDestino !== $posDeVerticeOrigen) {
            /** @var array<AdyacenteConPeso> $adyacentesDelDestino */
            $adyacentesDelDestino = &$this->listaDeAdyacencias[$posDeVerticeDestino];
            $adyacentesDelDestino[] = new AdyacenteConPeso($posDeVerticeOrigen, $peso);

            // PHP usort para ordenar la lista de adyacencias del destino
            usort($adyacentesDelDestino, function (AdyacenteConPeso $a, AdyacenteConPeso $b) {
                return $a->compareTo($b);
            });
        }
    }

    // public function eliminarVertice(T $unVertice): void { } // Se queda pendiente

    public function cantidadDeAristas(): int
    {
        $cantidadDeAristas = 0;
        $n = $this->cantidadDeVertices();

        for ($i = 0; $i < $n; $i++) {
            $vertice = $this->getVerticePorPosicion($i);
            $adyacentes = $this->getAdyacentesDeVertices($vertice);
            foreach ($adyacentes as $adyacente) {
                // Solo contamos una vez (ej: (A, B) con (B, A)), contando solo si el adyacente es >= al vertice
                if ($adyacente >= $vertice) {
                    $cantidadDeAristas++;
                }
            }
        }
        return $cantidadDeAristas;
    }

    /**
     * @param mixed $verticeOrigen
     * @param mixed $verticeDestino
     * @return void
     * @throws ExcepcionAristaNoExiste
     */
    public function eliminarArista($verticeOrigen, $verticeDestino): void
    {
        if (!$this->existeAdyacencia($verticeOrigen, $verticeDestino)) {
            throw new ExcepcionAristaNoExiste();
        }

        $posDeVerticeOrigen = $this->getPosicionDeVertice($verticeOrigen);
        $posDeVerticeDestino = $this->getPosicionDeVertice($verticeDestino);

        /** @var array<AdyacenteConPeso> $adyacentesDelOrigen */
        $adyacentesDelOrigen = &$this->listaDeAdyacencias[$posDeVerticeOrigen];

        // Remover adyacente destino del origen
        $adyacenciaDestino = new AdyacenteConPeso($posDeVerticeDestino);
        $this->removerAdyacente($adyacentesDelOrigen, $adyacenciaDestino);

        if ($posDeVerticeDestino !== $posDeVerticeOrigen) {
            /** @var array<AdyacenteConPeso> $adyacentesDelDestino */
            $adyacentesDelDestino = &$this->listaDeAdyacencias[$posDeVerticeDestino];
            // Remover adyacente origen del destino
            $adyacenciaOrigen = new AdyacenteConPeso($posDeVerticeOrigen);
            $this->removerAdyacente($adyacentesDelDestino, $adyacenciaOrigen);
        }
    }

    /**
     * @param mixed $verticeOrigen
     * @param mixed $verticeDestino
     * @return float
     * @throws ExcepcionAristaNoExiste
     */
    public function getPeso($verticeOrigen, $verticeDestino): float
    {
        $this->validarVertice($verticeDestino);
        $this->validarVertice($verticeOrigen);
        if (!$this->existeAdyacencia($verticeOrigen, $verticeDestino)) {
            throw new ExcepcionAristaNoExiste();
        }

        $posDeVerticeOrigen = $this->getPosicionDeVertice($verticeOrigen);
        $posDeVerticeDestino = $this->getPosicionDeVertice($verticeDestino);

        /** @var array<AdyacenteConPeso> $adyacentesDelOrigen */
        $adyacentesDelOrigen = $this->listaDeAdyacencias[$posDeVerticeOrigen];
        $adyacenciaDestinoBuscada = new AdyacenteConPeso($posDeVerticeDestino);

        foreach ($adyacentesDelOrigen as $adyacente) {
            if ($adyacente->equals($adyacenciaDestinoBuscada)) {
                return $adyacente->getPeso();
            }
        }

        // Esto no debería pasar si existeAdyacencia devolvió true
        throw new ExcepcionAristaNoExiste();
    }

    /**
     * Función auxiliar para remover un AdyacenteConPeso de un array.
     * @param array<AdyacenteConPeso> $lista
     * @param AdyacenteConPeso $adyacenteARemover
     * @return void
     */
    private function removerAdyacente(array &$lista, AdyacenteConPeso $adyacenteARemover): void
    {
        foreach ($lista as $i => $adyacente) {
            if ($adyacente->equals($adyacenteARemover)) {
                unset($lista[$i]);
                $lista = array_values($lista); // Reindexar el array
                return;
            }
        }
    }

    public function hayCiclos(): bool
    {
        // La implementación es compleja y se basa en DFS con un grafo auxiliar para
        // verificar si al visitar un adyacente ya marcado, no existe arista en el
        // grafo auxiliar (árbol de recorrido), implicando un retroceso (back edge) y, por lo tanto, un ciclo.
        // Se asume que la clase ControlMarcados y las excepciones están disponibles.

        $grafoAux = new GrafoPesado();
        $controlMarcados = new ControlMarcados($this->cantidadDeVertices());

        $vertices = $this->getVertices();
        foreach ($vertices as $vertice) {
            $grafoAux->insertarVertice($vertice);
        }

        $posDeVertice = 0;
        do {
            $verticeEnTurno = $this->getVerticePorPosicion($posDeVertice);

            $this->validarVertice($verticeEnTurno);
            // Usamos un array como pila (Stack)
            /** @var array<int> $pilaDeVertices */
            $pilaDeVertices = [];
            $pilaDeVertices[] = $posDeVertice; // push
            $controlMarcados->marcarVertice($posDeVertice);

            while (!empty($pilaDeVertices)) {
                $posDeVerticeAProcesar = array_pop($pilaDeVertices);

                $adyacentesDelVertice = $this->getAdyacentesDeVertices($this->getVerticePorPosicion($posDeVerticeAProcesar));

                foreach ($adyacentesDelVertice as $adyacente) {
                    $posDeAdyacente = $this->getPosicionDeVertice($adyacente);
                    try {
                        if (!$controlMarcados->estaVerticeMarcado($posDeAdyacente)) {
                            $pilaDeVertices[] = $posDeAdyacente; // push
                            $controlMarcados->marcarVertice($posDeAdyacente);
                            // Insertar arista en el árbol de recorrido (grafo auxiliar)
                            $peso = $this->getPeso($this->getVerticePorPosicion($posDeVerticeAProcesar), $adyacente);
                            $grafoAux->insertarArista($this->getVerticePorPosicion($posDeVerticeAProcesar), $adyacente, $peso);
                        } else {
                            // Si está marcado, verificamos si es un ciclo (no existe en el grafo auxiliar)
                            if (!$grafoAux->existeAdyacencia($this->getVerticePorPosicion($posDeVerticeAProcesar), $adyacente)) {
                                return true; // ¡Ciclo detectado!
                            }
                        }
                    } catch (ExcepcionAristaYaExiste $ex) {
                        // Ignorar, ya que solo buscamos si existe una adyacencia no marcada
                    }
                }
            }
            $posDeVertice = $this->posDeVerticeNoMarcado($controlMarcados);
        } while ($posDeVertice !== self::POS_DE_VERTICE_INVALIDO);

        return false;
    }

    /**
     * Busca el primer vértice no marcado.
     * @param ControlMarcados $controlMarcados
     * @return int
     */
    public function posDeVerticeNoMarcado(ControlMarcados $controlMarcados): int
    {
        for ($i = 0; $i < $this->cantidadDeVertices(); $i++) {
            if (!$controlMarcados->estaVerticeMarcado($i)) {
                return $i;
            }
        }
        return self::POS_DE_VERTICE_INVALIDO;
    }

    /**
     * @param array<array<float>> $matrizDeWarshall
     * @return bool
     */
    public function esConexo(array $matrizDeWarshall): bool
    {
        $n = count($matrizDeWarshall);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($matrizDeWarshall[$i][$j] == 0.0) { // Comparar con float (0.0)
                    return false;
                }
            }
        }
        return true;
    }
}