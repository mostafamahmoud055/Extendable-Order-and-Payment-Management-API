<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class MeTest extends TestCase
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

    public function test_authenticated_user_can_get_profile()
    {
        $token = JWTAuth::fromUser($this->user);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'User retrieved successfully',
                'errors' => [],
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                ],
                'errors',
            ]);

        $this->assertEquals($this->user->email, $response->json('data.email'));
    }

    public function test_unauthenticated_user_cannot_access_profile()
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthenticated',
                'data' => [],
                'errors' => [],
            ]);
    }
}