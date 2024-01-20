<?php

namespace App\Entity;

class OrderShipment
{
    public function __construct(
        public int    $id,
        public int    $orderId,
        public int    $shippingProviderId,
        public string $status
    )
    {
    }
}