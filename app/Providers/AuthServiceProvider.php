<?php

namespace App\Providers;

use App\Models\Item;
use App\Models\Loan;
use App\Policies\ItemPolicy;
use App\Policies\LoanPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Map Eloquent models to policies.
     */
    protected $policies = [
        Item::class => ItemPolicy::class,
        Loan::class => LoanPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate for admin role
        Gate::define('admin', fn($user) => (bool) ($user->is_admin ?? false));
    }
}
