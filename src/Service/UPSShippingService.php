<?php

namespace App\Service;

use App\Entity\OrderShipment;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UPSShippingService implements ShippingServiceInterface
{
    const API_URL = 'https://upsfake.com/';

    public function __construct(private readonly HttpClientInterface $client)
    {
    }

    public function register(OrderShipment $shipment): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        // TODO: add validation of data

        // TODO: map the data from input
        $requestJson = json_encode([[
            'order_id' => $shipment->orderId,
            'country' => 'LT',
            'street' => 'abc',
            'city' => 'Vilnius',
            'post_code' => 77777
        ]], JSON_THROW_ON_ERROR);
        $response = $this->client->request(
            'POST',
            self::API_URL . 'register', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => $requestJson,
        ]);

        return $response;
    }

    public function getName()
    {
        return 'ups';
    }
}