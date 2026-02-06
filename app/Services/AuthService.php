<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\AuthException;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = Auth::login($user);

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    public function login(array $credentials): array
    {
        if (!$token = Auth::attempt($credentials)) {
            return [
                'user'  => null,
                'token' => null,
            ];
        }

        return [
            'user'  => Auth::user(),
            'token' => $token,
        ];
    }

    public function logout(): void
    {
        Auth::logout();
    }

    public function me(): User
    {
        return Auth::user();
    }

    public function refresh(): array
    {
        $newToken = Auth::refresh();

        return [
            'user'  => Auth::user(),
            'token' => $newToken,
        ];
    }
}
