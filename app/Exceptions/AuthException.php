<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class AuthException extends BaseApiException
{
    public function __construct(
        string $message = 'Authentication error',
        int $statusCode = Response::HTTP_UNAUTHORIZED
    ) {
        parent::__construct($message, $statusCode);
    }
}
