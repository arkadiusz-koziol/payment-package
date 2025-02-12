<?php

namespace Skilleton\PaymentPackage\Providers;

use Illuminate\Support\ServiceProvider;
use Skilleton\PaymentPackage\Managers\PaymentManager;
use Skilleton\PaymentPackage\Services\PayUService;
use Skilleton\PaymentPackage\Services\Przelewy24Service;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/payment.php', 'payment');

        $this->app->singleton('payment.payu', function ($app) {
            return new PayUService();
        });

        $this->app->singleton('payment.przelewy24', function ($app) {
            return new Przelewy24Service();
        });

        $this->app->singleton('payment', function ($app) {
            return new PaymentManager($app);
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/payment.php' => config_path('payment.php'),
        ], 'config');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
    }
}
