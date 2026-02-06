<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class OrderException extends BaseApiException
{
    public function __construct(
        string $message = 'Order error',
        int $statusCode = Response::HTTP_BAD_REQUEST
    ) {
        parent::__construct($message, $statusCode);
    }
}
