<?php

declare(strict_types=1);

namespace App\Contracts;


use Symfony\Contracts\HttpClient\ResponseInterface;

interface ShippingServiceInterface
{
    public function register(OrderShipmentDTOInterface $shipment): ResponseInterface;

    public function supportsProvider(string $provider): bool;

    public function getDTOClass(): string;
}