<?php
declare(strict_types=1);


namespace App\Tests\Unit\Service;

use App\Contracts\OrderShipmentDTOInterface;
use App\Enums\ShippingProvider;
use App\Service\UPSShippingService;
use ArgumentCountError;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

class UPSShippingServiceTest extends KernelTestCase
{
    private UPSShippingService $service;

    public function setUP(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->service = $container->get(UPSShippingService::class);
        $this->service->initMockedClient();

    }

    /**
     * @test
     */
    public function getNameUpsProviderProperValue()
    {
        $providerName = $this->service->getName();
        $this->assertIsString($providerName, 'Name is not a string');
        //TODO: probably should use pure hardcoded value "ups" instead of ShippingProvider::Ups->value
        $this->assertEquals(ShippingProvider::Ups->value, $providerName, $providerName . ' is not a equal to ' . ShippingProvider::Ups->value);
    }

    /**
     * @test
     */
    public function supportsProviderUpsCheck()
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
    public function getDtoClassProvidesProperValue()
    {
        $upsDtoClassName = 'App\DTO\UPSOrderShipment';
        $currentDTOName = $this->service->getDTOClass();
        $this->assertEquals($upsDtoClassName, $currentDTOName, $upsDtoClassName . ' is not a equal to ' . $currentDTOName);
    }

    /**
     * @test
     */
    public function initMockedClientInstanceCheck()
    {
        $mockedHttpClient = $this->service->initMockedClient();
        $this->assertInstanceOf(MockHttpClient::class, $mockedHttpClient, 'Http client is not a MockHttpClient');
    }

    /**
     * @test
     */
    public function registerHttpRequestCheck()
    {
        $dtoStub = new class() implements OrderShipmentDTOInterface {
            private string $data = 'test';
            private int $id = 1;

            /**
             * @return mixed
             */
            public function jsonSerialize(): mixed
            {
                return get_object_vars($this);
            }
        };

        $response = $this->service->register($dtoStub);
        $this->assertInstanceOf(ResponseInterface::class, $response, 'Returned value is not related to ResponseInterface');

    }

}