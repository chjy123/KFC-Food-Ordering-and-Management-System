<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\Order;
use App\Models\Cart;
use App\Observers\OrderObserver;
use App\Observers\CartObserver;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Order::observe(OrderObserver::class);
        Cart::observe(CartObserver::class);
    }
}
