<?php

namespace Skilleton\PaymentPackage\Services;

use OpenPayU_Configuration;
use OpenPayU_Order;

class PayUService
{
    public function __construct()
    {
        $merchantPosId  = config('payment.payu.merchant_key');
        $signatureKey   = config('payment.payu.salt');
        $clientId       = config('payment.payu.merchant_key');
        $clientSecret   = config('payment.payu.auth_header');
        $testMode       = config('payment.payu.test_mode', true);

        OpenPayU_Configuration::setEnvironment(
            $testMode ? 'sandbox' : 'secure'
        );
        OpenPayU_Configuration::setMerchantPosId($merchantPosId);
        OpenPayU_Configuration::setSignatureKey($signatureKey, 'MD5');

        OpenPayU_Configuration::setOauthClientId($clientId);
        OpenPayU_Configuration::setOauthClientSecret($clientSecret);
    }

    public function processPayment(array $data)
    {
        $order = [
            'notifyUrl'     => route('payment.payu.verify'),
            'continueUrl'   => route('test.payu.verify'),
            'customerIp'    => request()->ip(),
            'merchantPosId' => config('payment.payu.merchant_key'),
            'description'   => $data['productinfo'] ?? 'Order description',
            'currencyCode'  => 'PLN',
            'totalAmount'   => (int) ($data['amount'] * 100),
            'extOrderId'    => $data['txnid'] ?? ('TXN' . time()),
            'buyer' => [
                'email'     => $data['email'] ?? 'test@example.com',
                'phone'     => $data['phone'] ?? '600000000',
                'firstName' => $data['firstname'] ?? 'TestName',
                'lastName'  => $data['lastname'] ?? 'TestLastName',
            ],
            'products' => [
                [
                    'name'      => $data['productinfo'] ?? 'Sample Product',
                    'unitPrice' => (int) ($data['amount'] * 100),
                    'quantity'  => 1,
                ]
            ],
        ];

        $response = OpenPayU_Order::create($order);

        if ($response->getStatus() == 'SUCCESS') {
            $redirectUri = $response->getResponse()->redirectUri;

            return redirect($redirectUri);
        }

        throw new \Exception("PayU create order failed: " . $response->getStatus());
    }

    public function verifyPayment(array $data)
    {
        $orderId = $data['orderId'] ?? null;
        if (!$orderId) {
            return ['error' => 'Brak orderId w danych od PayU'];
        }

        $response = OpenPayU_Order::retrieve($orderId);
        if ($response->getStatus() == 'SUCCESS') {
            $orderData = $response->getResponse()->orders[0] ?? null;
            return [
                'orderId'    => $orderData->orderId,
                'extOrderId' => $orderData->extOrderId,
                'status'     => $orderData->status,
            ];
        }

        return ['error' => 'Błąd weryfikacji zamówienia: ' . $response->getStatus()];
    }
}
