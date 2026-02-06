# Extendable Order & Payment Management API

A Laravel-based API for managing orders and payments with fully extensible payment gateways.


## Table of Contents

- [Requirements](#requirements)  
- [Installation](#installation)  
- [Environment Setup](#environment-setup)  
- [Database Setup](#database-setup)  
- [Running Tests](#running-tests)  
- [API Documentation](#api-documentation)  
- [Payment Gateway Extensibility](#payment-gateway-extensibility)


## Requirements

- PHP >= 8.1  
- Composer  
- MySQL / SQLite  
- Laravel >= 12  


## Installation

1. Clone the repository:

```bash
gh repo clone mostafamahmoud055/Extendable-Order-and-Payment-Management-API
cd extendable-order-payment-api
```

2. Install dependencies:

```bash
composer install
```

3. Copy the `.env.example` to `.env` and configure your environment variables:

```bash
cp .env.example .env
```

4. Generate application key:

```bash
php artisan key:generate
```


## Environment Setup

- Database configuration:

```env
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

> For local development you can use MySQL or SQLite.  

- JWT Secret:

```bash
php artisan jwt:secret
```

Note: This command only updates .env.
For testing environment (.env.testing), you need to copy the same JWT_SECRET manually.


## Database Setup

Run migrations:

```bash
php artisan migrate
```

## Running Tests

Feature and Unit tests are included for authentication, orders, payments, and payment gateways.

Run all tests:

```bash
php artisan test
```


## API Documentation

A Postman collection is included in the project:

`ExtendableOrderPaymentAPI.postman_collection.json`  

It contains all endpoints for:

- Authentication (`/api/auth`)  
- Orders (`/api/orders`)  
- Payments (`/api/payments`)  
- Payment Gateways (`/api/gateways`)  

Each request includes example body, headers, and response.


## Payment Gateway Extensibility

The system is designed to allow **adding new payment gateways easily** without modifying existing code.  

**Structure:**

1. **Gateway Service:** `App\Services\PaymentGatewayService`  
   - Returns the appropriate gateway instance based on the requested method.

2. **Gateway Interface:** All gateways implement a common interface with a `pay()` method.  

3. **Payment Service:** `App\Services\PaymentService`  
   - Calls the gateway's `pay()` method.  
   - Handles payment status (`SUCCESSFUL` or `FAILED`).  
   - Creates payment records in the database.

4. **Adding a New Gateway:**
   - Create a new class implementing the gateway interface.
   - Add the gateway to `PaymentGatewayEnum`.
   - Register it in `PaymentGatewayService`.
   - No changes are required in controllers or payment logic.

**Example:**

```php
namespace App\Gateways;

use App\Contracts\PaymentGatewayInterface;

class StripeGateway implements PaymentGatewayInterface
{
    public function pay(array $data): array
    {
        // Stripe API integration here
        return [
            'status' => 'success',
            'transaction_id' => 'stripe_123456'
        ];
    }
}
```

Then in `PaymentGatewayService`:

```php
public function getGateway(string $method)
{
    return match ($method) {
        'CREDIT_CARD' => new CreditCardGateway(),
        'PAYPAL' => new PaypalGateway(),
        'STRIPE' => new StripeGateway(),
        default => ['error' => 'Gateway not found']
    };
}
```

This allows the API to process any new gateway without touching the payment service logic.

