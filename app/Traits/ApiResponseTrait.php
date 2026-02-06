<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Success response
     */
    public function successResponse($data = [], string $message = '', int $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'errors' => []
        ], $statusCode);
    }

    /**
     * Error response
     */
    public function errorResponse(string $message = '', int $statusCode = 400, $errors = []): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => [],
            'errors' => $errors,
        ], $statusCode);
    }
}
