# Laravel-Brevo Integration Package

Complete Brevo (formerly Sendinblue) integration for Laravel, including email support, webhook handling, and optional Vue components.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bhekor/laravel-brevo.svg?style=flat-square)](https://packagist.org/packages/bhekor/laravel-brevo)
[![Total Downloads](https://img.shields.io/packagist/dt/bhekor/laravel-brevo.svg?style=flat-square)](https://packagist.org/packages/bhekor/laravel-brevo)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/bhekor/laravel-brevo/tests.yml?label=tests)](https://github.com/bhekor/laravel-brevo/actions?query=workflow%3Arun-tests+branch%3Amain)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat-square)](https://phpstan.org)
[![License](https://img.shields.io/packagist/l/bhekor/laravel-brevo.svg?style=flat-square)](https://github.com/bhekor/laravel-brevo/blob/main/LICENSE)
[![PHP Version Support](https://img.shields.io/packagist/php-v/bhekor/laravel-brevo.svg?style=flat-square)](https://php.net)
[![Laravel Version Support](https://img.shields.io/badge/Laravel-9.x%20%7C%2010.x-FF2D20.svg?style=flat-square&logo=laravel)](https://laravel.com)

---

## Features

- Seamless integration with Laravel's Mail facade
- Full support for Markdown Mailables
- Multiple usage options (default mailer, explicit mailer, facade)
- Webhook handling
- Optional Vue components for analytics
- Comprehensive error handling
- Modern PHP & Laravel practices (strict typing, PSR-12, PHPDoc)

---

## Installation

```bash
composer require bhekor/laravel-brevo
```

### Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=brevo-config
```

Set your API key and sender details in your `.env`:

```ini
MAIL_MAILER=brevo
BREVO_API_KEY=your_api_key
BREVO_FROM_EMAIL=noreply@example.com
BREVO_FROM_NAME="Your App"
```

---

## Frontend Integration (Optional)

Install frontend dependencies:

```bash
npm install --save-dev @vueuse/core chart.js
php artisan vendor:publish --tag=brevo-assets
```

In your `resources/js/app.js`:

```javascript
import Brevo from '../../public/vendor/laravel-brevo/brevo';
import { createApp } from 'vue';

const app = createApp({});
app.use(Brevo, {
    apiBaseUrl: '/brevo-api'
});
```

Use components in your Vue template:

```vue
<template>
  <BrevoStats />
  <BrevoWebhookLogs />
</template>
```

---

## Usage

### Regular Mailable

```php
Mail::to('user@example.com')->send(new OrderShipped());
```

### Markdown Mailable

```php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->markdown('emails.orders.shipped')
                    ->with([
                        'order' => $this->order,
                    ]);
    }
}
```

### Using the Facade

```php
Brevo::email()->send(new OrderShipped());
```

---

## Webhooks

1. Register your webhook endpoint in the Brevo dashboard.
2. Add the following to your `routes/api.php`:

```php
Route::brevoWebhooks('/brevo/webhook');
```

---

## Testing

```bash
composer test
```

---

## Security Vulnerabilities

Please report any security vulnerabilities to [security@bhekor.com](mailto:security@bhekor.com).


---

## Test Suite

The package includes a robust set of unit and feature tests to ensure reliability.

### Directory Structure

```
tests/
├── Feature/
│   ├── MailTransportTest.php
│   └── WebhookTest.php
└── Unit/
    ├── BrevoClientTest.php
    ├── EventMapperTest.php
    ├── MailTransportTest.php
    ├── TransactionalEmailTest.php
    └── WebhookControllerTest.php
```

Run all tests with:

```bash
composer test
```

Or use PHPUnit directly:

```bash
./vendor/bin/phpunit
```


---

## Package Structure

```
bhekor/
└── laravel-brevo/
    ├── config/
    │   └── brevo.php
    ├── resources/
    │   └── js/
    │       ├── components/
    │       │   ├── BrevoStats.vue
    │       │   └── BrevoWebhookLogs.vue
    │       └── brevo.js
    ├── src/
    │   ├── BrevoServiceProvider.php
    │   ├── Contracts/
    │   │   ├── BrevoClientInterface.php
    │   │   └── MailTransportInterface.php
    │   ├── Exceptions/
    │   │   ├── BrevoApiException.php
    │   │   ├── BrevoConfigurationException.php
    │   │   └── BrevoValidationException.php
    │   ├── Facades/
    │   │   └── Brevo.php
    │   ├── Mail/
    │   │   └── BrevoTransport.php
    │   ├── Services/
    │   │   ├── BrevoClient.php
    │   │   └── TransactionalEmail.php
    │   └── Webhooks/
    │       ├── EventMapper.php
    │       ├── WebhookController.php
    │       └── routes.php
    ├── tests/
    │   ├── Feature/
    │   │   ├── MailTransportTest.php
    │   │   └── WebhookTest.php
    │   └── Unit/
    │       ├── BrevoClientTest.php
    │       ├── EventMapperTest.php
    │       ├── MailTransportTest.php
    │       ├── TransactionalEmailTest.php
    │       └── WebhookControllerTest.php
    ├── composer.json
    ├── package.json
    └── README.md
```
