<?php
declare(strict_types=1);

namespace App\Service;

use App\Contracts\MockedShippingServiceInterface;
use App\Contracts\OrderShipmentDTOInterface;
use App\Contracts\ShippingServiceInterface;
use App\DTO\OMNIVAOrderShipment;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class OMNIVAShippingService implements ShippingServiceInterface, MockedShippingServiceInterface
{
    const API_URL = 'https://omnivafake.com';

    public function __construct(private HttpClientInterface $client)
    {
    }

    public function register(OrderShipmentDTOInterface $shipment): ResponseInterface
    {
        $response = $this->client->request(
            'POST',
            self::API_URL . '/pickup/find', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode([[
                'country' => $shipment->getCountry(),
                'post_code' => $shipment->getPostCode()
            ]]),
        ]);

        if (!in_array($response->getStatusCode(), ['200', '201'])) {
            return $response;
        }
        $pickupData = json_decode($response->getContent());
        $response = $this->client->request(
            'POST',
            self::API_URL . '/register', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => json_encode([[
                'pickup_point_id' => $pickupData->data->attributes->pickup_point_id,
                'order_id' => $shipment->getOrderId(),
            ]]),
        ]);

        return $response;
    }

    public function getName(): string
    {
        return 'omniva';
    }

    public function supportsProvider(string $provider): bool
    {
        return $provider === 'omniva';
    }

    public function getDTOClass(): string
    {
        return OMNIVAOrderShipment::class;
    }

    public function initMockedClient(): MockHttpClient
    {
        $this->client = new MockHttpClient();
        $this->client->setResponseFactory([
            new MockResponse('{"data":{"type":"pickup_point","attributes":{"pickup_point_id": 11111}}}', ['http_code' => 200]),
            new MockResponse('{"data":{"type":"parcel","attributes":{"parcel_id": 22222,"provider": "omniva"}}}', ['http_code' => 200])
        ]);

        return $this->client;
    }
}