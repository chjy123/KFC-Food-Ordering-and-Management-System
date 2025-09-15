<?php
#author’s name： Lim Jing Min
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\Bus\CommandBus;

class CommandBusServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->singleton(CommandBus::class, fn($app) => new CommandBus($app));
    }
}
