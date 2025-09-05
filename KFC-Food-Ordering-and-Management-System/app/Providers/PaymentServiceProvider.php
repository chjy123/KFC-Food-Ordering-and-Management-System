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
        //
    }

    public function boot()
    {
        //
    }
}
