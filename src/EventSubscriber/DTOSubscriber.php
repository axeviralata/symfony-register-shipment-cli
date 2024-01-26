<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Contracts\OrderShipmentDTOValidatorInterface;
use App\Event\DTOPostCreationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DTOSubscriber implements EventSubscriberInterface
{

    public function __construct(private readonly OrderShipmentDTOValidatorInterface $validator)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DTOPostCreationEvent::NAME => 'validate'
        ];
    }

    /**
     * Validate DTO object after creation
     * @param DTOPostCreationEvent $event
     * @return void
     */
    public function validate(DTOPostCreationEvent $event): void
    {
        $dto = $event->getDto();
        $this->validator->validateDTO($dto);
    }
}