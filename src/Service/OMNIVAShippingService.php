<?php

namespace App\Service;

use App\Entity\OrderShipment;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OMNIVAShippingService implements ShippingServiceInterface
{
    const API_URL = 'https://omnivafake.com';

    public function __construct(private readonly HttpClientInterface $client)
    {
    }

    public function register(OrderShipment $shipment): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        // TODO: add validation of data if needed

        //1
        $requestJson1 = json_encode([[
            'country' => 'LT',
            'post_code' => 77777
        ]], JSON_THROW_ON_ERROR);
        $response = $this->client->request(
            'POST',
            self::API_URL . '/pickup/find', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => $requestJson1,
        ]);

        if (!in_array($response->getStatusCode(), ['200', '201'])) {
            return $response;
        }
        //2

        $pickupData = json_decode($response->getContent());
        $requestJson2 = json_encode([[
            'pickup_point_id' => $pickupData->data->attributes->pickup_point_id,
            'order_id' => $shipment->orderId,
        ]], JSON_THROW_ON_ERROR);
        $response = $this->client->request(
            'POST',
            self::API_URL . '/register', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => $requestJson2,
        ]);

        return $response;
        // TODO: Implement register() method.
    }

    public function getName()
    {
        return 'omniva';
    }
}