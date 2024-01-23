<?php

declare(strict_types=1);

namespace App\Service;

use App\Contracts\MockedShippingServiceInterface;
use App\Contracts\OrderShipmentDTOInterface;
use App\Contracts\ShippingServiceInterface;
use App\DTO\OMNIVAOrderShipment;
use App\Enums\ShippingProvider;
use InvalidArgumentException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
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
        if (!$shipment instanceof OMNIVAOrderShipment) {
            throw new InvalidArgumentException($shipment::class . ' not an instance of a OMNIVAOrderShipment.');
        }
        $response = $this->client->request(
            'POST',
            self::API_URL . '/pickup/find',
            [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                'body' => json_encode([
                    [
                        'country' => $shipment->getCountry(),
                        'post_code' => $shipment->getPostCode()
                    ]
                ]),
            ]
        );
        if ($response->getStatusCode() < Response::HTTP_OK || $response->getStatusCode() > Response::HTTP_IM_USED) {
            return $response;
        }
        $pickupData = json_decode($response->getContent());
        $response = $this->client->request(
            'POST',
            self::API_URL . '/register',
            [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                'body' => json_encode([
                    [
                        'pickup_point_id' => $pickupData->data->attributes->pickup_point_id,
                        'order_id' => $shipment->getOrderId(),
                    ]
                ]),
            ]
        );

        return $response;
    }

    public function getName(): string
    {
        return ShippingProvider::Omniva->value;
    }

    public function supportsProvider(string $provider): bool
    {
        return $this->getName() === $provider;
    }

    public function getDTOClass(): string
    {
        return OMNIVAOrderShipment::class;
    }

    public function initMockedClient(): MockHttpClient
    {
        $this->client = new MockHttpClient();
        $this->client->setResponseFactory([
            new MockResponse(
                '{"data":{"type":"pickup_point","attributes":{"pickup_point_id": 11111}}}',
                ['http_code' => Response::HTTP_OK]
            ),
            new MockResponse(
                '{"data":{"type":"parcel","attributes":{"parcel_id": 22222,"provider": "omniva"}}}',
                ['http_code' => Response::HTTP_OK]
            )
        ]);

        return $this->client;
    }
}