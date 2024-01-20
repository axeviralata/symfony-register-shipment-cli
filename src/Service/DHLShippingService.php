<?php

namespace App\Service;

use App\Entity\OrderShipment;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DHLShippingService implements ShippingServiceInterface
{
    const API_URL = 'https://dhlfake.com';

    public function __construct(private readonly HttpClientInterface $client)
    {
    }

    public function register(OrderShipment $shipment): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        // TODO: add validation of data


        // TODO: map the data from input
        $requestJson = json_encode([[
            'order_id' => 1,
            'country' => 'LT',
            'address' => 'abc',
            'town' => 'Vilnius',
            'zip_code' => 77777
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

        if (!in_array($response->getStatusCode(), ['200', '201'])) {
            throw new Exception('Response status code is different than expected.');
        }

        return $response;
        // TODO: Implement register() method.
    }

    public function getName()
    {
        return 'dhl';
    }
}