<?php

namespace App\Services\Opay\Exceptions;

use Throwable;

class OpayApiException extends OpayException
{
    public function __construct(
        string $message,
        public readonly ?string $opayCode = null,
        public readonly ?string $opayMessage = null,
        public readonly ?array $rawResponse = null,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
