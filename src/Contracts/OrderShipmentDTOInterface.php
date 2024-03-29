<?php

declare(strict_types=1);

namespace App\Contracts;

interface OrderShipmentDTOInterface extends \JsonSerializable
{
    public function getOrderId(): ?int;
}