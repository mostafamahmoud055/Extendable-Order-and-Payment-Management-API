<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtAuthenticate
{
    public function handle($request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated',
                'data' => [],
                'errors' => [
                    'token' => [$e->getMessage()]
                ]
            ], 401);
        }

        return $next($request);
    }
}
