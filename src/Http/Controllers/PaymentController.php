<?php

namespace Skilleton\PaymentPackage\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Skilleton\PaymentPackage\Services\PayUService;
use Skilleton\PaymentPackage\Services\Przelewy24Service;

class PaymentController extends Controller
{

    public function __construct(
        protected PayUService $payu,
        protected Przelewy24Service $przelewy24
    ) {
    }

    /**
     * Initiate a PayU transaction.
     * This will generate an HTML form and submit it automatically.
     * @throws \OpenPayU_Exception
     */
    public function payuCheckout(Request $request): array
    {
        return $this->payu->processPayment($request->all());
    }

    /**
     * Initiate a Przelewy24 transaction.
     */
    public function przelewy24Checkout(Request $request)
    {
        return $this->przelewy24->createTransaction($request->all());
    }

    /**
     * Verify the PayU payment after redirection.
     */
    public function payuVerify(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->payu->verifyPayment($request->all())
        ]);
    }
}
