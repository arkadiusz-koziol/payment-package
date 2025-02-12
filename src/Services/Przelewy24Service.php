<?php

namespace Skilleton\PaymentPackage\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Przelewy24Service
{
    protected $client;
    protected $config;

    public function __construct()
    {
        $this->client = new Client();
        $this->config = config('payment.przelewy24');
    }

    /**
     * Tworzy transakcję w REST API Przelewy24
     */
    public function createTransaction(array $data)
    {
        // Wybieramy odpowiedni endpoint: sandbox vs production
        $url = $this->config['test_mode']
            ? 'https://sandbox.przelewy24.pl/api/v1/transaction/register'
            : 'https://secure.przelewy24.pl/api/v1/transaction/register';

        // Przelewy24 oczekuje Basic Auth: base64(posId:apiKey),
        // a w ciele JSON m.in. merchantId, posId, sessionId, amount...
        $posId  = $this->config['pos_id'];
        $apiKey = $this->config['api_key'];

        // Budujemy tablicę z danymi transakcji w formacie zgodnym z dokumentacją
        $payload = [
            'merchantId' => (int) $this->config['merchant_id'],
            'posId'      => (int) $posId,
            'sessionId'  => $data['sessionId'] ?? 'SESSION-' . time(),
            'amount'     => (int) $data['p24_amount'] ?? 0, // kwota w groszach
            'currency'   => $data['p24_currency'] ?? 'PLN',
            'description'=> $data['p24_description'] ?? 'Test payment',
            'email'      => $data['p24_email'] ?? 'test@example.com',
            'country'    => $data['p24_country'] ?? 'PL',
            'language'   => $data['p24_language'] ?? 'pl',
            'urlReturn'  => $data['p24_urlReturn'] ?? route('p24.return'), // Twój endpoint powrotu
            'urlStatus'  => $data['p24_urlStatus'] ?? route('p24.status'), // Twój endpoint callback statusu
            // ewentualnie inne pola typu shipping, cart, itd.
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
            // Możemy przechwycić i obsłużyć błąd 4xx/5xx
            return [
                'error'   => true,
                'message' => $e->getMessage(),
                'body'    => $e->hasResponse() ? (string) $e->getResponse()->getBody() : null,
            ];
        }

        return json_decode($response->getBody(), true);
    }
}
