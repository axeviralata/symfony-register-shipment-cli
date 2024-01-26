<?php

declare(strict_types=1);

namespace App\Event;

use App\Contracts\OrderShipmentDTOInterface;
use Symfony\Contracts\EventDispatcher\Event;

class DTOPostCreationEvent extends Event
{
    public const NAME = 'dto.created';

    public function __construct(private readonly OrderShipmentDTOInterface $dto)
    {
    }

    public function getDto(): OrderShipmentDTOInterface
    {
        return $this->dto;
    }
}