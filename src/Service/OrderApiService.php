<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OrderApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchOrders($userId): array
    {
        $response = $this->client->request('GET', 'https://127.0.0.1:8000/api/orders', [
            'query' => ['userId' => $userId],
            'timeout' => 360,
            'verify_peer' => false,
            'verify_peer' => false,
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to fetch orders');
        }

        return $response->toArray();
    }
}
