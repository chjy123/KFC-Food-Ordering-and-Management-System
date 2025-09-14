<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();

        
        Gate::define('isAdmin', fn (User $u) => $u->role === 'admin');

        
        Gate::before(fn (User $u) => $u->role === 'admin' ? true : null);
    }
}
