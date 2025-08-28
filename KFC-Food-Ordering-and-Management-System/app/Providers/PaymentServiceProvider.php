<?php
#author’s name： Pang Jun Meng
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PaymentService;
use App\Payments\Handlers\CardSimHandler;
use App\Payments\Handlers\WalletSimHandler;

class PaymentServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind handlers
        $this->app->bind(CardSimHandler::class, function ($app) {
            return new CardSimHandler();
        });

        $this->app->bind(WalletSimHandler::class, function ($app) {
            return new WalletSimHandler();
        });

        // Bind PaymentService as singleton so controller can inject it
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService($app->make(\App\Services\OrderClient::class));
        });
    }

    public function boot()
    {
        //
    }
}
