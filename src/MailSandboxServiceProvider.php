<?php

namespace AshishDhakal\MailSandbox;

use AshishDhakal\MailSandbox\SandboxTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class MailSandboxServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mail-sandbox.php', 'mail-sandbox');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/mail-sandbox.php' => config_path('mail-sandbox.php'),
            ], 'mail-sandbox-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'mail-sandbox-migrations');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/mail-sandbox'),
            ], 'mail-sandbox-views');
        }

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'mail-sandbox');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        Mail::extend('sandbox', function () {
            return new SandboxTransport();
        });
    }
}
