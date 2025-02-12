<?php

namespace Skilleton\PaymentPackage\Services;

use GuzzleHttp\Client;

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

        $response = $this->client->post($url, [
            'json' => $data,
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->config['merchant_id'] . ':' . $this->config['crc_key']),
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

}
