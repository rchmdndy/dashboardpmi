<?php

namespace App\Providers;

use App\Models\UserTransaction;
use App\Observers\UserTransactionObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Midtrans\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        Gate::define('admin', fn ($user) => $user->role_id === 1);
        Gate::define('staff', fn ($user) => $user->role_id === 3);
        Gate::define('customer', fn ($user) => $user->role_id === 4);
        UserTransaction::observe(UserTransactionObserver::class);

    }
}
