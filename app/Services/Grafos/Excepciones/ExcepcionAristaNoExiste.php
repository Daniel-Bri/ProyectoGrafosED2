<?php

namespace App\Services\Grafos\Excepciones;

use RuntimeException;

class ExcepcionAristaNoExiste extends RuntimeException
{
    public function __construct(string $message = "Arista indicada no existe en su grafo", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}