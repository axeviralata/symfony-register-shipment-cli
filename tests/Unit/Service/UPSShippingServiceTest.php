<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\DTO\DHLOrderShipment;
use App\DTO\UPSOrderShipment;
use App\Enums\ShippingProvider;
use App\Service\UPSShippingService;
use App\Tests\Helpers\SerializerHelper;
use ArgumentCountError;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class UPSShippingServiceTest extends TestCase
{
    private UPSShippingService $service;
    private MockHttpClient $client;

    public function setUP(): void
    {
        $this->client = new MockHttpClient();
        $this->client->setResponseFactory([
            new MockResponse(
                '{"data":{"type":"parcel","attributes":{"parcel_id": 22222,"provider": "ups"}}}',
                ['http_code' => Response::HTTP_OK]
            ),
        ]);
        $serializer = $this->createMock(SerializerHelper::class);
        $serializer->method('serialize')
            ->willReturn('{"iam":"json"}');
        $this->service = new UPSShippingService($this->client, $serializer);
    }

    /**
     * @test
     */
    public function getNameUpsProviderProperValue(): void
    {
        $providerName = $this->service->getName();
        $this->assertIsString($providerName, 'Name is not a string');
        $this->assertEquals(
            ShippingProvider::Ups->value,
            $providerName,
            $providerName . ' is not a equal to ' . ShippingProvider::Ups->value
        );
    }

    /**
     * @test
     */
    public function supportsProviderUpsCheck(): void
    {
        $provider = 'ups';
        $supportsCheck = $this->service->supportsProvider($provider);
        $this->assertTrue($supportsCheck, 'Current service supports only UPS');

        $provider = 'fedex';
        $supportsCheck = $this->service->supportsProvider($provider);
        $this->assertFalse($supportsCheck, 'Current service supports only UPS');

        $this->expectException(ArgumentCountError::class);
        $this->service->supportsProvider();
    }

    /**
     * @test
     */
    public function getDtoClassProvidesProperValue(): void
    {
        $upsDtoClassName = 'App\DTO\UPSOrderShipment';
        $currentDTOName = $this->service->getDTOClass();
        $this->assertEquals(
            $upsDtoClassName,
            $currentDTOName,
            $upsDtoClassName . ' is not a equal to ' . $currentDTOName
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
    public function registerUpsHttpRequestCheck(): void
    {
        $dto = new UPSOrderShipment();
        $dto->setCountry('country');
        $dto->setOrderId(1);
        $response = $this->service->register($dto);
        $this->assertInstanceOf(
            ResponseInterface::class,
            $response,
            'Returned value is not related to ResponseInterface'
        );

        $wrongDto = new DHLOrderShipment();
        $this->expectException(\InvalidArgumentException::class);
        $this->service->register($wrongDto);
    }

}