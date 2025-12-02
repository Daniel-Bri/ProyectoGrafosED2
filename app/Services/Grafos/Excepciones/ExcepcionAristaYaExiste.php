<?php

namespace App\Services\Grafos\Excepciones;

use RuntimeException;

class ExcepcionAristaYaExiste extends RuntimeException
{
    public function __construct(string $message = "Arista indicada ya existe en su grafo", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}