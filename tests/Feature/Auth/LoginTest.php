<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    private User $user;
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'mostafa@gmail.com',
            'password' => Hash::make('123456'),
        ]);
    }

    public function test_user_can_login_successfully()
    {
        $loginData = [
            'email' => 'mostafa@gmail.com',
            'password' => '123456',
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Login successful',
                'errors' => [],
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token',
                ],
                'errors',
            ]);

        $this->assertEquals($this->user->email, $response->json('data.user.email'));
        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_login_validation_error()
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Errors',
                'data' => [],
                'errors' => [
                    'email' => ['Email is required'],
                    'password' => ['Password is required'],
                ],
            ]);
    }

    public function test_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'mostafa@gmail.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Invalid credentials',
                'data' => [],
                'errors' => [],
            ]);
    }
}
