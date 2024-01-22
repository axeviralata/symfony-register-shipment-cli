<?php
declare(strict_types=1);

namespace App\Contracts;


interface ShippingServiceInterface
{
    public function register(OrderShipmentDTOInterface $shipment);

    public function supportsProvider(string $provider): bool;

    public function getDTOClass(): string;
}