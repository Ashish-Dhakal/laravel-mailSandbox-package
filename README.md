# Mail Sandbox for Laravel

A Laravel 12 package that intercepts outgoing emails and stores them locally for viewing via an in-app web interface. This is extremely useful in development environments – instead of sending real emails, you can capture them and inspect their content in a browser.

## Features

- Intercepts all outgoing emails.
- Stores emails in a local database.
- Web interface to list and view captured emails.
- Configurable route prefix and middleware.
- Easy to enable/disable via environment variables.

## Installation

1. Install the package via Composer:

```bash
composer require ashishdhakal/mail-sandbox
```

2. Publish the configuration and migrations (optional):

```bash
php artisan vendor:publish --tag=mail-sandbox-config
php artisan vendor:publish --tag=mail-sandbox-migrations
```

3. Run the migrations:

```bash
php artisan migrate
```

## Configuration

You can configure the package in `config/mail-sandbox.php`:

```php
return [
    'capture_enabled' => env('MAIL_SANDBOX_CAPTURE', config('app.env') !== 'production'),
    'ui_enabled' => env('MAIL_SANDBOX_UI', config('app.env') !== 'production'),
    'path' => 'mail-sandbox',
    'middleware' => ['web'],
];
```

## Usage

Once installed and enabled, any email sent by your application will be intercepted and stored. You can view them at `/mail-sandbox`.

## Testing

To run the package's test suite, ensure you have installed the dev dependencies and run:

```bash
composer test
```

The tests are powered by [Pest](https://pestphp.com/).

## Credits

- Ashish Dhakal (ashishdhakal433@gmail.com)
