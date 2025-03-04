<?php

namespace Skilleton\PaymentPackage\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class Przelewy24Service
{
    protected $client;
    protected $config;

    public function __construct()
    {
        $this->client = new Client();
        $this->config = config('payment.przelewy24');
    }

    public function createTransaction(array $data)
    {
        $url = $this->config['test_mode']
            ? 'https://sandbox.przelewy24.pl/api/v1/transaction/register'
            : 'https://secure.przelewy24.pl/api/v1/transaction/register';

        $posId  = $this->config['pos_id'];
        $apiKey = $this->config['api_key'];

        $payload = [
            'merchantId' => (int) $this->config['merchant_id'],
            'posId'      => (int) $posId,
            'sessionId'  => $data['sessionId'] ?? 'SESSION-' . time(),
            'amount'     => (int) $data['p24_amount'] ?? 0,
            'currency'   => $data['p24_currency'] ?? 'PLN',
            'description'=> $data['p24_description'] ?? 'Test payment',
            'email'      => $data['p24_email'] ?? 'test@example.com',
            'country'    => $data['p24_country'] ?? 'PL',
            'language'   => $data['p24_language'] ?? 'pl',
            'urlReturn'  => $data['p24_urlReturn'] ?? $this->config['return'],
            'urlStatus'  => $data['p24_urlStatus'] ?? $this->config['status'],
        ];

        try {
            $response = $this->client->post($url, [
                'json'    => $payload,
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($posId . ':' . $apiKey),
                    'Content-Type'  => 'application/json'
                ],
            ]);
        } catch (ClientException $e) {
            return [
                'error'   => true,
                'message' => $e->getMessage(),
                'body'    => $e->hasResponse() ? (string) $e->getResponse()->getBody() : null,
            ];
        } catch (GuzzleException $e) {
            return [
                'error'   => true,
                'message' => $e->getMessage(),
            ];
        }

        return json_decode($response->getBody(), true);
    }
}
