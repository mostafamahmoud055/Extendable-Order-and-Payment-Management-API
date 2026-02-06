<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class PaymentException extends BaseApiException
{
    public function __construct(
        string $message = 'Payment Gateway error',
        int $statusCode = Response::HTTP_BAD_REQUEST
    ) {
        parent::__construct($message, $statusCode);
    }
}
