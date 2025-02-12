<?php

namespace Skilleton\PaymentPackage\Managers;

use Illuminate\Contracts\Foundation\Application;
use Skilleton\PaymentPackage\Services\PayUService;
use Skilleton\PaymentPackage\Services\Przelewy24Service;

class PaymentManager
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function payu(): PayUService
    {
        return $this->app->make('payment.payu');
    }

    public function przelewy24(): Przelewy24Service
    {
        return $this->app->make('payment.przelewy24');
    }
}
