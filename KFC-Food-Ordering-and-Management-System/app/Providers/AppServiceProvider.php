<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Stripe client as a singleton
        $this->app->singleton(StripeClient::class, function () {
            return new StripeClient(config('services.stripe.secret'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production for secure cookies and sessions
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
