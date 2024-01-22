<?php
declare(strict_types=1);

namespace App\Factory;

use App\Contracts\ShippingServiceInterface;

class ShippingServiceFactory
{
    private iterable $shippingServices;

    public function __construct(iterable $shippingServices)
    {
        $this->shippingServices = $shippingServices;
    }

    /**
     * Create an instance of ShippingServiceInterface based on a tagged services
     * @param string $provider
     * @return ShippingServiceInterface|null
     */
    public function create(string $provider): ?ShippingServiceInterface
    {
        foreach ($this->shippingServices as $shippingService) {
            if ($shippingService instanceof ShippingServiceInterface && $shippingService->supportsProvider($provider)) {
                return $shippingService;
            }
        }

        throw new \InvalidArgumentException(sprintf('No shipping service found for provider "%s"', $provider));
    }
}