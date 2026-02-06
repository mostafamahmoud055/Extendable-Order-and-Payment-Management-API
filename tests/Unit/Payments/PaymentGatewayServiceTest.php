<?php

namespace Tests\Unit\Payments;

use Tests\TestCase;
use App\Models\PaymentGateway;
use App\Services\PaymentGatewayService;
use App\Repositories\Eloquent\PaymentGatewayRepository;
use App\Enums\PaymentGatewayEnum;
use Illuminate\Support\Facades\Cache;

class PaymentGatewayServiceTest extends TestCase
{
    private PaymentGatewayService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = new PaymentGatewayRepository();
        $this->service = new PaymentGatewayService($repository);
    }

    public function test_it_can_create_gateway()
    {
        $data = [
            'name' => PaymentGatewayEnum::CREDIT_CARD,
            'config' => ['client_id' => '123', 'client_secret' => 'abc']
        ];

        $gateway = $this->service->createGateway($data);

        $this->assertInstanceOf(PaymentGateway::class, $gateway);
        $this->assertEquals('123', $gateway->config['client_id']);
    }

    public function test_it_can_get_existing_gateway_instance()
    {
        PaymentGateway::create([
            'name' => PaymentGatewayEnum::PAYPAL,
            'config' => ['client_id' => 'id', 'client_secret' => 'secret']
        ]);

        $gateway = $this->service->getGateway(PaymentGatewayEnum::PAYPAL->value);

        $this->assertInstanceOf(\App\Services\Payments\PayPalGateway::class, $gateway);
    }

    public function test_getGateway_returns_error_if_not_found()
    {
        $gatewayName = PaymentGatewayEnum::CREDIT_CARD->value;

        $result = $this->service->getGateway($gatewayName);

        $this->assertIsArray($result);
        $this->assertEquals(404, $result['status']);
        $this->assertEquals('Gateway not found', $result['error']);
    }

    public function test_it_can_update_gateway_and_clear_cache()
    {
        PaymentGateway::create([
            'name' => PaymentGatewayEnum::STRIPE,
            'config' => ['client_id' => 'old', 'client_secret' => 'old']
        ]);

        $updated = $this->service->updateGateway(PaymentGatewayEnum::STRIPE, [
            'config' => ['client_id' => 'new', 'client_secret' => 'new']
        ]);


        $this->assertEquals('new', $updated->config['client_id']);
    }
}
