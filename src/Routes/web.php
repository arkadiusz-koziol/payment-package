<?php

use Illuminate\Support\Facades\Route;
use Skilleton\PaymentPackage\Http\Controllers\PaymentController;

Route::group(['prefix' => 'payment', 'middleware' => ['web']], function () {
    Route::post('/payu/checkout', [PaymentController::class, 'payuCheckout'])
        ->name('payment.payu.checkout');
    Route::get('/payu/verify', [PaymentController::class, 'payuVerify'])
        ->name('payment.payu.verify');

    Route::post('/przelewy24/checkout', [PaymentController::class, 'przelewy24Checkout'])
        ->name('payment.przelewy24.checkout');
});
