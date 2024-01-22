<?php
declare(strict_types=1);

namespace App\Service;

use App\Contracts\MockedShippingServiceInterface;
use App\Contracts\OrderShipmentDTOInterface;
use App\Contracts\ShippingServiceInterface;
use App\DTO\UPSOrderShipment;
use App\Enums\ShippingProvider;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class UPSShippingService implements ShippingServiceInterface, MockedShippingServiceInterface
{
    const API_URL = 'https://upsfake.com/';

    public function __construct(private HttpClientInterface $client, private SerializerInterface $serializer)
    {
    }

    public function register(OrderShipmentDTOInterface $shipment): ResponseInterface
    {
        $requestJsonData = $this->serializer->serialize($shipment, 'json');
        $response = $this->client->request(
            'POST',
            self::API_URL . 'register', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => $requestJsonData,
        ]);

        return $response;
    }

    public function getName(): string
    {
        return ShippingProvider::Ups->value;
    }

    public function supportsProvider(string $provider): bool
    {
        return $provider === $this->getName();
    }

    public function getDTOClass(): string
    {
        return UPSOrderShipment::class;
    }

    public function initMockedClient(): MockHttpClient
    {
        $this->client = new MockHttpClient();
        $this->client->setResponseFactory([
            new MockResponse('{"data":{"type":"parcel","attributes":{"parcel_id": 22222,"provider": "ups"}}}', ['http_code' => 200])
        ]);

        return $this->client;
    }
}