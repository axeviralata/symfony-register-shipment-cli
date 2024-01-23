<?php

declare(strict_types=1);

namespace App\Tests\Integration\Factory;

use App\Enums\ShippingProvider;
use App\Factory\ShippingServiceFactory;
use App\Service\DHLShippingService;
use App\Service\OMNIVAShippingService;
use App\Service\UPSShippingService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ShippingServiceFactoryTest extends KernelTestCase
{
    private $factory;

    public function setUP(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->factory = $container->get(ShippingServiceFactory::class);
    }

    /**
     * @test
     */
    public function createInstance(): void
    {
        $provider = ShippingProvider::Ups->value;
        $shippingProviderInstance = $this->factory->create($provider);
        $this->assertInstanceOf(UPSShippingService::class, $shippingProviderInstance, 'Provider');

        $provider = ShippingProvider::Dhl->value;
        $shippingProviderInstance = $this->factory->create($provider);
        $this->assertInstanceOf(DHLShippingService::class, $shippingProviderInstance, 'Provider');

        $provider = ShippingProvider::Omniva->value;
        $shippingProviderInstance = $this->factory->create($provider);
        $this->assertInstanceOf(OMNIVAShippingService::class, $shippingProviderInstance, 'Provider');

        $provider = 'AzerothWyvernDelivery';
        $this->expectException(\InvalidArgumentException::class);
        $this->factory->create($provider);
    }
}