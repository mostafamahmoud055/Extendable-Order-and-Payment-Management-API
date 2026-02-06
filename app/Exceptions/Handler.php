<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        
        if ($e instanceof BaseApiException) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => [],
            ], $e->getStatusCode());
        }

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'errors' => []
        ], 500);
    }
}
