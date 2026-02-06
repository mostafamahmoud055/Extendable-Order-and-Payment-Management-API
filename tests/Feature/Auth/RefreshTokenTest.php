<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class RefreshTokenTest extends TestCase
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

    public function test_authenticated_user_can_refresh_token()
    {
        // login first to get token
        $token = JWTAuth::fromUser($this->user);

        $response = $this->postJson('/api/auth/refresh', [], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'token',
                ],
                'errors',
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Token refreshed successfully',
                'data' => [
                    'user' => [
                        'email' => 'mostafa@gmail.com',
                    ],
                ],
                'errors' => [],
            ]);

        // ensure new token exists
        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_unauthenticated_user_cannot_refresh_token()
    {
        $response = $this->postJson('/api/auth/refresh');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthenticated',
                'data' => [],
                'errors' => [],
            ]);
    }
}
