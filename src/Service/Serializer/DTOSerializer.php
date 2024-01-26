<?php

declare(strict_types=1);

namespace App\Service\Serializer;

use App\Event\DTOPostCreationEvent;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class DTOSerializer implements SerializerInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function serialize(mixed $data, string $format, array $context = []): string
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        $dto = $this->serializer->deserialize($data, $type, $format, $context);
        $event = new DTOPostCreationEvent($dto);
        $this->eventDispatcher->dispatch($event, $event::NAME);

        return $dto;
    }
}