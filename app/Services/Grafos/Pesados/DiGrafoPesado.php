<?php

namespace App\Services\Grafos\Pesados;

use App\Services\Grafos\Excepciones\ExcepcionAristaNoExiste;
use App\Services\Grafos\Excepciones\ExcepcionAristaYaExiste;

class DiGrafoPesado extends GrafoPesado
{
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

        // Validación de vértices se hace en existeAdyacencia / GrafoPesado
        $posDeVerticeOrigen = $this->getPosicionDeVertice($verticeOrigen);
        $posDeVerticeDestino = $this->getPosicionDeVertice($verticeDestino);

        /** @var array<AdyacenteConPeso> $adyacentesDelOrigen */
        $adyacentesDelOrigen = &$this->listaDeAdyacencias[$posDeVerticeOrigen];
        $adyacentesDelOrigen[] = new AdyacenteConPeso($posDeVerticeDestino, $peso);
        
        // Ordenar la lista de adyacencias
        usort($adyacentesDelOrigen, function (AdyacenteConPeso $a, AdyacenteConPeso $b) {
            return $a->compareTo($b);
        });

        // NOTA: No insertamos arista de destino a origen, ya que es un Dígrafo.
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

        // NOTA: No eliminamos arista de destino a origen, ya que es un Dígrafo.
    }
    
    /**
     * Método auxiliar para remover un AdyacenteConPeso de un array (necesario en PHP).
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
    
    // El resto de los métodos se heredan de GrafoPesado
    
    // Falta implementar: hayCiclos, gradoDelVertice, etc. adaptados a Dígrafos (grado de entrada/salida). 
    // Para el proyecto, puedes asumir que la versión de GrafoPesado es suficiente para hayCiclos, 
    // o centrarte solo en Dijkstra (que no requiere hayCiclos).
}