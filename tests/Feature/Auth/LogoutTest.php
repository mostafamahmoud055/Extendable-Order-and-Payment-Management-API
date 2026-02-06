<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutTest extends TestCase
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

    public function test_authenticated_user_can_logout()
    {
        $token = jwtAuth::fromUser($this->user);

        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Logged out successfully',
                'data' => [],
                'errors' => [],
            ]);
    }

    public function test_unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthenticated',
                'data' => [],
                'errors' => [],
            ]);
    }
}
