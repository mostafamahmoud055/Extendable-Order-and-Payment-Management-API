<?php

namespace Tests\Feature\Order;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderTest extends TestCase
{
    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'mostafa@gmail.com',
            'password' => Hash::make('123456'),
        ]);

        $this->token = JWTAuth::fromUser($this->user);
    }

    public function test_authenticated_user_can_create_order()
    {
        $payload = [
            'order' => [],
            'items' => [
                ['product_name' => 'Product A', 'quantity' => 2, 'price' => 50],
                ['product_name' => 'Product B', 'quantity' => 1, 'price' => 30],
            ],
        ];

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/orders', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'status',
                    'total_amount',
                    'items' => [
                        ['id', 'product_name', 'quantity', 'price', 'total']
                    ],
                    'created_at',
                    'updated_at',
                ],
                'errors',
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total_amount' => 130,
        ]);

        $this->assertDatabaseCount('order_items', 2);
    }

    public function test_user_can_get_all_their_orders()
    {
        Order::factory()->for($this->user)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['data', 'links', 'meta'],
                'errors',
            ]);
    }

    public function test_user_can_get_order_by_id()
    {
        $order = Order::factory()->for($this->user)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'errors' => [],
            ])
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'status', 'total_amount', 'items', 'created_at', 'updated_at'],
            ]);
    }

    public function test_user_can_update_order()
    {
        $order = Order::factory()->for($this->user)->create();
        $payload = [
            'items' => [
                ['product_name' => 'Updated Product', 'quantity' => 3, 'price' => 20],
            ]
        ];

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/orders/{$order->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'total_amount', 'items'],
                'errors',
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'total_amount' => 60,
        ]);
    }

    public function test_user_can_delete_order_without_payments()
    {
        $order = Order::factory()->for($this->user)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Order deleted successfully',
            ]);

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_user_cannot_delete_order_with_payments()
    {
        $order = Order::factory()->for($this->user)->create();
        $order->payments()->create([
            'payment_method' => 'paypal',
            'status' => 'successful',
            'total_amount' => $order->total_amount,
            'reference_id' => 'ref_123',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(400)
            ->assertJson(
                [
                    'status' => 'error',
                    'message' => 'Order deletion failed',
                    'data' => [],
                    'errors' => [
                        'order_id' => 'Order cannot be deleted because it has associated payments'
                    ]
                ]
            );
    }

    public function test_unauthenticated_user_cannot_access_orders()
    {
        $response = $this->getJson('/api/orders');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthenticated',
                'data' => [],
                'errors' => [],
            ]);
    }
}
