<?php
declare(strict_types=1);


namespace App\Tests\Unit\Service;

use App\Contracts\OrderShipmentDTOInterface;
use App\Enums\ShippingProvider;
use App\Service\OMNIVAShippingService;
use ArgumentCountError;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

class OMNIVAShippingServiceTest extends KernelTestCase
{
    private OMNIVAShippingService $service;

    public function setUP(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->service = $container->get(OMNIVAShippingService::class);
        $this->service->initMockedClient();

    }

    /**
     * @test
     */
    public function getNameUpsProviderProperValue()
    {
        $providerName = $this->service->getName();
        $this->assertIsString($providerName, 'Name is not a string');
        $this->assertEquals(ShippingProvider::Omniva->value, $providerName, $providerName . ' is not a equal to ' . ShippingProvider::Ups->value);
    }

    /**
     * @test
     */
    public function supportsProviderUpsCheck()
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
    public function getDtoClassProvidesProperValue()
    {
        $omnivaDtoClassName = 'App\DTO\OMNIVAOrderShipment';
        $currentDTOName = $this->service->getDTOClass();
        $this->assertEquals($omnivaDtoClassName, $currentDTOName, $omnivaDtoClassName . ' is not a equal to ' . $currentDTOName);
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
            private string $order_id = 'test';
            private string $country = 'LT';
            private int $post_code = 77777;

            public function getCountry()
            {
                return $this->country;
            }

            public function getPostCode()
            {
                return $this->post_code;
            }

            public function getOrderId()
            {
                return $this->order_id;
            }

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