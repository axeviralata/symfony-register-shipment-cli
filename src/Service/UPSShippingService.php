<?php

declare(strict_types=1);

namespace App\Service;

use App\Contracts\MockedShippingServiceInterface;
use App\Contracts\OrderShipmentDTOInterface;
use App\Contracts\ShippingServiceInterface;
use App\DTO\UPSOrderShipment;
use App\Enums\ShippingProvider;
use App\Service\Serializer\DTOSerializer;
use InvalidArgumentException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class UPSShippingService implements ShippingServiceInterface, MockedShippingServiceInterface
{
    const API_URL = 'https://upsfake.com/';

    public function __construct(private HttpClientInterface $client, private DTOSerializer $serializer)
    {
    }

    /**
     * @param OrderShipmentDTOInterface $shipment
     * @return ResponseInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function register(OrderShipmentDTOInterface $shipment): ResponseInterface
    {
        if (!$shipment instanceof UPSOrderShipment) {
            throw new InvalidArgumentException($shipment::class . ' not an instance of a UPSOrderShipment.');
        }
        $requestJsonData = $this->serializer->serialize($shipment, 'json');

        $response = $this->client->request(
            'POST',
            self::API_URL . 'register',
            [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                'body' => $requestJsonData,
            ]
        );

        return $response;
    }

    /**
     * @param string $orderJson
     * @return UPSOrderShipment
     */
    public function createDTO(string $orderJson): UPSOrderShipment
    {
        return $this->serializer->deserialize($orderJson, $this->getDTOClass(), 'json');
    }

    public function getName(): string
    {
        return ShippingProvider::Ups->value;
    }

    public function supportsProvider(string $provider): bool
    {
        return $this->getName() === $provider;
    }

    public function getDTOClass(): string
    {
        return UPSOrderShipment::class;
    }

    public function initMockedClient(): MockHttpClient
    {
        $this->client = new MockHttpClient();
        $this->client->setResponseFactory([
            new MockResponse(
                '{"data":{"type":"parcel","attributes":{"parcel_id": 22222,"provider": "ups"}}}',
                ['http_code' => Response::HTTP_OK]
            )
        ]);

        return $this->client;
    }
}