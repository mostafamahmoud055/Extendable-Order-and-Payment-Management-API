<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AuthResource;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected AuthService $authService) {}

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());
        return $this->successResponse([
            'user'  => new AuthResource($result['user']),
            'token' => $result['token'],
        ], 'Registered successfully', 201);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        if (!$result['token']) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        return $this->successResponse([
            'user'  => new AuthResource($result['user']),
            'token' => $result['token'],
        ], 'Login successful');
    }

    public function me()
    {
        $user = $this->authService->me();
        return $this->successResponse(new AuthResource($user), 'User retrieved successfully');
    }

    public function logout()
    {
        $this->authService->logout();
        return $this->successResponse( message:  'Logged out successfully');
    }

    public function refresh()
    {
        $result = $this->authService->refresh();
        return $this->successResponse([
            'user'  => new AuthResource($result['user']),
            'token' => $result['token'],
        ], 'Token refreshed successfully');
    }
}
