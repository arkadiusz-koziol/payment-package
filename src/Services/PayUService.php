<?php

namespace Skilleton\PaymentPackage\Services;

use Skilleton\PaymentPackage\PayU\PayU;

class PayUService
{
    protected $payu;

    public function __construct()
    {
        $this->payu = new PayU();
        $this->payu->key = config('payment.payu.merchant_key');
        $this->payu->salt = config('payment.payu.salt');
        $this->payu->env_prod = !config('payment.payu.test_mode');
        $this->payu->initGateway();
    }

    /**
     * Initiate a payment transaction using PayU.
     * This generates and submits an HTML payment form.
     */
    public function processPayment(array $data)
    {
        return $this->payu->showPaymentForm($data);
    }

    /**
     * Verify a transaction based on PayU response.
     */
    public function verifyPayment(array $data)
    {
        return $this->payu->verifyPayment($data);
    }

    /**
     * Get transaction details by transaction ID.
     */
    public function getTransactionByTxnId($txnid)
    {
        return $this->payu->getTransactionByTxnId($txnid);
    }

    /**
     * Get transaction details by PayU ID.
     */
    public function getTransactionByPayuId($payuid)
    {
        return $this->payu->getTransactionByPayuId($payuid);
    }
}
