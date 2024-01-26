<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Serializer;

use App\DTO\OMNIVAOrderShipment;
use App\Event\DTOPostCreationEvent;
use App\Service\Serializer\DTOSerializer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class DTOSerializerTest extends TestCase
{

    /**
     * @test
     */
    public function deserializeDispatchingEventCheck()
    {
        $dto = new OMNIVAOrderShipment();
        $dto->setOrderId(1);

        $serializerMock = $this->createMock(SerializerInterface::class);
        $serializerMock->expects($this->once())
            ->method('deserialize')
            ->with('{"order_id":1}', OMNIVAOrderShipment::class, 'json')
            ->willReturn($dto);

        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DTOPostCreationEvent::class), DTOPostCreationEvent::NAME);

        $dtoSerializer = new DTOSerializer($serializerMock, $eventDispatcherMock);
        $result = $dtoSerializer->deserialize('{"order_id":1}', OMNIVAOrderShipment::class, 'json');
        $this->assertEquals($dto, $result);
    }
}