<?php

namespace App\Service;

use App\Entity\OrderShipment;
use Symfony\Component\HttpFoundation\Response;

interface ShippingServiceInterface
{
    public function register(OrderShipment $shipment);
}