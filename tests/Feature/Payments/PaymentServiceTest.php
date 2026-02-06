<?php

namespace Tests\Feature\Payment;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Enums\PaymentGatewayEnum;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class PaymentServiceTest extends TestCase
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

        PaymentGateway::create([
            'name' => PaymentGatewayEnum::CREDIT_CARD,
            'config' => [
                'client_id' => 'dummy',
                'client_secret' => 'dummy',
            ],
        ]);

        PaymentGateway::create([
            'name' => PaymentGatewayEnum::PAYPAL,
            'config' => [
                'client_id' => 'dummy',
                'client_secret' => 'dummy',
            ],
        ]);
    }

    public function test_user_can_process_credit_card_payment()
    {
        $order = Order::factory()->for($this->user)->create([
            'status' => 'confirmed',
            'total_amount' => 100,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson("/api/payments/process/{$order->id}", [
                'payment_method' => PaymentGatewayEnum::PAYPAL->value,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Payment processed successfully',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'order_id',
                    'payment_method',
                    'status',
                    'reference_id',
                    'amount',
                    'order',
                    'created_at',
                    'updated_at',
                ],
                'errors',
            ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'payment_method' => PaymentGatewayEnum::PAYPAL->value,
            'total_amount' => 100,
        ]);
    }

    public function test_user_cannot_process_payment_for_unconfirmed_order()
    {
        $order = Order::factory()->for($this->user)->create([
            'status' => 'pending',
            'total_amount' => 100,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson("/api/payments/process/{$order->id}", [
                'payment_method' => PaymentGatewayEnum::CREDIT_CARD->value,
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'errors' => ['payment_error' => 'Order cannot be processed for payment'],
            ]);
    }

    public function test_user_can_get_all_payments()
    {
        $order = Order::factory()->for($this->user)->create(['status' => 'confirmed']);
        Payment::factory()->for($order)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/payments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['data', 'links', 'meta'],
                'errors',
            ]);
    }

    public function test_user_can_get_payment_by_reference_id()
    {
        $order = Order::factory()->for($this->user)->create(['status' => 'confirmed']);
        $payment = Payment::factory()->for($order)->create([
            'payment_method' => PaymentGatewayEnum::PAYPAL->value,
            'status' => 'successful',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/payments/{$payment->reference_id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Payment retrieved successfully',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'order_id',
                    'payment_method',
                    'status',
                    'reference_id',
                    'amount',
                    'order',
                    'created_at',
                    'updated_at',
                ],
                'errors',
            ]);
    }

    public function test_unauthenticated_user_cannot_access_payments()
    {
        $response = $this->getJson('/api/payments');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthenticated',
                'data' => [],
                'errors' => [],
            ]);
    }
}
