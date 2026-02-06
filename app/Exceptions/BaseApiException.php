<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseApiException extends Exception
{
    protected int $statusCode;

    public function __construct(
        string $message = 'Application error',
        int $statusCode = Response::HTTP_BAD_REQUEST
    ) {
        $this->statusCode = $statusCode;
        parent::__construct($message, $statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
