<?php
declare(strict_types=1);


namespace App\Tests\Unit\Service;

use App\Contracts\OrderShipmentDTOInterface;
use App\Enums\ShippingProvider;
use App\Service\DHLShippingService;
use ArgumentCountError;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DHLShippingServiceTest extends KernelTestCase
{
    private DHLShippingService $service;

    public function setUP(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->service = $container->get(DHLShippingService::class);
        $this->service->initMockedClient();
    }

    /**
     * @test
     */
    public function getNameDhlProviderProperValue()
    {
        $providerName = $this->service->getName();
        $this->assertIsString($providerName, 'Name is not a string');
        $this->assertEquals(ShippingProvider::Dhl->value, $providerName, $providerName . ' is not a equal to ' . ShippingProvider::Ups->value);
    }

    /**
     * @test
     */
    public function supportsProviderDhlCheck()
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
    public function getDtoClassProvidesProperValue()
    {
        $dhlDtoClassName = 'App\DTO\DHLOrderShipment';
        $currentDTOName = $this->service->getDTOClass();
        $this->assertEquals($dhlDtoClassName, $currentDTOName, $dhlDtoClassName . ' is not a equal to ' . $currentDTOName);
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
        $dto = new class() implements OrderShipmentDTOInterface {
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

        $response = $this->service->register($dto);
        $this->assertInstanceOf(ResponseInterface::class, $response, 'Returned value is not related to ResponseInterface');

    }

}