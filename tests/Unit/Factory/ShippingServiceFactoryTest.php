<?php

namespace App\Tests\Unit\Factory;

use App\Contracts\ShippingServiceInterface;
use App\Enums\ShippingProvider;
use App\Factory\ShippingServiceFactory;
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
    public function createInstance()
    {
        $provider = ShippingProvider::Ups->value;
        $shippingProviderInstance = $this->factory->create($provider);

        $this->assertInstanceOf(ShippingServiceInterface::class, $shippingProviderInstance, 'Provider');

        $provider = 'AzerothVyvernlines';
        $this->expectException(\InvalidArgumentException::class);
        $shippingProviderInstance = $this->factory->create($provider);

    }
}