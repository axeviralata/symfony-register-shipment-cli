<?php
declare(strict_types=1);

namespace App\Service;

use App\Contracts\MockedShippingServiceInterface;
use App\Contracts\OrderShipmentDTOInterface;
use App\Contracts\ShippingServiceInterface;
use App\DTO\DHLOrderShipment;
use Exception;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DHLShippingService implements ShippingServiceInterface, MockedShippingServiceInterface
{
    const API_URL = 'https://dhlfake.com';

    public function __construct(private HttpClientInterface $client, private SerializerInterface $serializer)
    {
    }

    /**
     * Registration of a shipment
     * @param OrderShipmentDTOInterface $shipment
     * @return ResponseInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function register(OrderShipmentDTOInterface $shipment): ResponseInterface
    {
        $requestJsonData = $this->serializer->serialize($shipment, 'json');
        $response = $this->client->request(
            'POST',
            self::API_URL . '/register', [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'body' => $requestJsonData,
        ]);

        if (!in_array($response->getStatusCode(), ['200', '201'])) {
            throw new Exception('Response status code is different than expected.');
        }

        return $response;
    }

    /**
     * Provider name
     * @return string
     */
    public function getName(): string
    {
        return 'dhl';
    }

    /**
     * @param string $provider
     * @return bool
     */
    public function supportsProvider(string $provider): bool
    {
        return $provider === 'dhl';
    }

    /**
     * Mock response for request to have a proper data
     * @return MockHttpClient
     */
    public function initMockedClient(): MockHttpClient
    {
        $this->client = new MockHttpClient();
        $this->client->setResponseFactory([
            new MockResponse('{"data":{"type":"parcel","attributes":{"parcel_id": 22222,"provider": "dhl"}}}', ['http_code' => 200]),
        ]);

        return $this->client;
    }

    /**
     * Data transfer object class name related to this service
     * @return string
     */
    public function getDTOClass(): string
    {
        return DHLOrderShipment::class;
    }
}