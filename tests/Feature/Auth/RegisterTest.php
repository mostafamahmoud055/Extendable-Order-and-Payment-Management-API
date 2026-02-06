<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    public function test_user_can_register_successfully()
    {
        $userData = [
            'name' => 'Mostafa Mahmoud',
            'email' => 'mostafa@gmail.com',
            'password' => '123456',
            'password_confirmation' => '123456',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Registered successfully',
                'data' => [
                    'user' => [
                        'name' => 'Mostafa Mahmoud',
                        'email' => 'mostafa@gmail.com',
                    ],
                ],
                'errors' => [],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Mostafa Mahmoud',
            'email' => 'mostafa@gmail.com',
        ]);

        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_register_validation_error()
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Errors',
                'data' => [],
                'errors' => [
                    'name' => ['Name is required'],
                    'email' => ['Email is required'],
                    'password' => ['Password is required'],
                ],
            ]);
    }
}
