<?php

declare(strict_types=1);


namespace App\Tests\Unit\Service;

use App\DTO\DHLOrderShipment;
use App\DTO\UPSOrderShipment;
use App\Enums\ShippingProvider;
use App\Service\DHLShippingService;
use App\Tests\Helpers\SerializerHelper;
use ArgumentCountError;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DHLShippingServiceTest extends TestCase
{
    private DHLShippingService $service;
    private MockHttpClient $client;

    public function setUP(): void
    {
        $this->client = new MockHttpClient();
        $this->client->setResponseFactory([
            new MockResponse(
                '{"data":{"type":"parcel","attributes":{"parcel_id": 22222,"provider": "dhl"}}}',
                ['http_code' => Response::HTTP_OK]
            ),
        ]);
        $serializer = $this->createMock(SerializerHelper::class);
        $serializer->method('serialize')
            ->willReturn('{"iam":"json"}');
        $this->service = new DHLShippingService($this->client, $serializer);
    }

    /**
     * @test
     */
    public function getNameDhlProviderProperValue(): void
    {
        $providerName = $this->service->getName();
        $this->assertIsString($providerName, 'Name is not a string');
        $this->assertEquals(
            ShippingProvider::Dhl->value,
            $providerName,
            $providerName . ' is not a equal to ' . ShippingProvider::Ups->value
        );
    }

    /**
     * @test
     */
    public function supportsProviderDhlCheck(): void
    {
        $provider = 'dhl';
        $supportsCheck = $this->service->supportsProvider($provider);
        $this->assertTrue($supportsCheck, 'Current service supports only DHL');

        $provider = 'fedex';
        $supportsCheck = $this->service->supportsProvider($provider);
        $this->assertFalse($supportsCheck, 'Current service supports only DHL');

        $this->expectException(ArgumentCountError::class);
        $this->service->supportsProvider();
    }

    /**
     * @test
     */
    public function getDtoClassProvidesProperValue(): void
    {
        $dhlDtoClassName = 'App\DTO\DHLOrderShipment';
        $currentDTOName = $this->service->getDTOClass();
        $this->assertEquals(
            $dhlDtoClassName,
            $currentDTOName,
            $dhlDtoClassName . ' is not a equal to ' . $currentDTOName
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
    public function registerDhlHttpRequestCheck(): void
    {
        $dto = new DHLOrderShipment();
        $dto->setCountry('country');
        $dto->setAddress('address');
        $dto->setOrderId(1);
        $dto->setTown('Vilnius');
        $dto->setZipCode(7777);


        $response = $this->service->register($dto);
        $this->assertInstanceOf(
            ResponseInterface::class,
            $response,
            'Returned value is not related to ResponseInterface'
        );

        $wrongDto = new UPSOrderShipment();
        $this->expectException(\InvalidArgumentException::class);
        $this->service->register($wrongDto);
    }

}