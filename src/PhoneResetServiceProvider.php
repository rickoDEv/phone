<?php

namespace RickoDev\PhoneReset;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class PhoneResetServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'phone-reset-migrations');

        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton('auth.phone-password', function ($app) {
            return new PhonePasswordBrokerManager($app);
        });

        $this->app->bind('auth.phone-password.broker', function ($app) {
            return $app->make('auth.phone-password')->broker();
        });
    }

    public function provides(): array
    {
        return ['auth.phone-password', 'auth.phone-password.broker'];
    }
}
