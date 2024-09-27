<?php

namespace App\Providers;

use App\Models\UserTransaction;
use App\Observers\UserTransactionObserver;
use App\Providers\FilamentServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Midtrans\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (env('APP_ENV') === 'production') {
            $this->app['request']->server->set('HTTPS', true);
            $this->app['config']->set('app.debug', false);
        }

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

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
