<?php

declare(strict_types=1);

namespace App\Tests\Integration\EventSubscriber;

use App\DTO\DHLOrderShipment;
use App\DTO\OMNIVAOrderShipment;
use App\Event\DtoPostCreationEvent;

use InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DTOSubscriberTest extends KernelTestCase
{
    private EventDispatcherInterface $eventDispatcher;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->eventDispatcher = $container->get(EventDispatcherInterface::class);
    }

    /**
     * @test
     */
    public function valiadateValidationException()
    {
        $dhlDto = new DHLOrderShipment();
        $dhlDto->setOrderId(1);
        $event = new DtoPostCreationEvent($dhlDto);

        $this->expectException(InvalidArgumentException::class);
        $this->eventDispatcher->dispatch($event, $event::NAME);
    }

    /**
     * @test
     */
    public function valiadateNoException()
    {
        $omnivaDto = new OMNIVAOrderShipment();
        $omnivaDto->setOrderId(1);
        $omnivaDto->setCountry('LT');
        $omnivaDto->setPostCode(77777);

        $event = new DtoPostCreationEvent($omnivaDto);
        try {
            $this->eventDispatcher->dispatch($event, $event::NAME);
        } catch (InvalidArgumentException $exception) {
            $this->fail('No exception should be thrown, but an exception was caught: ' . $exception->getMessage());
        }

        // No exception thrown during validation of DTO
        $this->assertTrue(true);
    }


}