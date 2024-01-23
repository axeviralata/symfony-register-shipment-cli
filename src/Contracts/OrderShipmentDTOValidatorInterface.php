<?php

declare(strict_types=1);

namespace App\Contracts;


interface OrderShipmentDTOValidatorInterface
{
    public function validateDTO(OrderShipmentDTOInterface $shipmentDTO): void;
}