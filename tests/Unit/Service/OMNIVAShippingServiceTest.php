<?php

declare(strict_types=1);


namespace App\Tests\Unit\Service;

use App\DTO\OMNIVAOrderShipment;
use App\DTO\UPSOrderShipment;
use App\Enums\ShippingProvider;
use App\Service\OMNIVAShippingService;
use ArgumentCountError;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class OMNIVAShippingServiceTest extends TestCase
{
    private OMNIVAShippingService $service;
    private MockHttpClient $client;

    public function setUP(): void
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
        $this->service = new OMNIVAShippingService($this->client);
    }

    /**
     * @test
     */
    public function getNameUpsProviderProperValue(): void
    {
        $providerName = $this->service->getName();
        $this->assertIsString($providerName, 'Name is not a string');
        $this->assertEquals(
            ShippingProvider::Omniva->value,
            $providerName,
            $providerName . ' is not a equal to ' . ShippingProvider::Ups->value
        );
    }

    /**
     * @test
     */
    public function supportsProviderUpsCheck(): void
    {
        $provider = 'omniva';
        $supportsCheck = $this->service->supportsProvider($provider);
        $this->assertTrue($supportsCheck, 'Current service supports only OMNIVA');

        $provider = 'fedex';
        $supportsCheck = $this->service->supportsProvider($provider);
        $this->assertFalse($supportsCheck, 'Current service supports only OMNIVA');

        $this->expectException(ArgumentCountError::class);
        $this->service->supportsProvider();
    }

    /**
     * @test
     */
    public function getDtoClassProvidesProperValue(): void
    {
        $omnivaDtoClassName = 'App\DTO\OMNIVAOrderShipment';
        $currentDTOName = $this->service->getDTOClass();
        $this->assertEquals(
            $omnivaDtoClassName,
            $currentDTOName,
            $omnivaDtoClassName . ' is not a equal to ' . $currentDTOName
        );
    }

    /**
     * @test
     */
    public function initMockedClientInstanceCheck(): void
    {
        $mockedHttpClient = $this->service->initMockedClient();
        $this->assertInstanceOf(MockHttpClient::class, $mockedHttpClient, 'Http client is not a MockHttpClient');
    }

    /**
     * @test
     */
    public function registerOmnivaHttpRequestCheck(): void
    {
        $dto = new OMNIVAOrderShipment();
        $dto->setCountry('country');
        $dto->setPostCode(77777);
        $dto->setOrderId(1);

        $response = $this->service->register($dto);
        $this->assertInstanceOf(
            ResponseInterface::class,
            $response,
            'Returned value is not related to ResponseInterface'
        );
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->client->setResponseFactory([
            new MockResponse(
                '{"data":{"type":"error","attributes":{"message": "BAD REQUEST"}}}',
                ['http_code' => Response::HTTP_BAD_REQUEST]
            ),
            new MockResponse(
                '{"data":{"type":"parcel","attributes":{"parcel_id": 22222,"provider": "omniva"}}}',
                ['http_code' => Response::HTTP_OK]
            )
        ]);
        $response = $this->service->register($dto);
        $this->assertInstanceOf(
            ResponseInterface::class,
            $response,
            'Returned value is not related to ResponseInterface'
        );
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $wrongDto = new UPSOrderShipment();
        $this->expectException(\InvalidArgumentException::class);
        $this->service->register($wrongDto);
    }

}