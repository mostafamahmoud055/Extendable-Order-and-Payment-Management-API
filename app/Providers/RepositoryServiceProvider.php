<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\PaymentGatewayRepository;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\PaymentGatewayRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(PaymentGatewayRepositoryInterface::class, PaymentGatewayRepository::class);

    }

    public function boot() {}
}
