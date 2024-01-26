<?php

declare(strict_types=1);

namespace App\Tests\Integration\Command;

use App\Command\OrderShipmentRegisterCommand;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class OrderShipmentRegisterCommandTest extends KernelTestCase
{
    private OrderShipmentRegisterCommand $command;
    private ReflectionMethod $execute;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->command = $container->get(OrderShipmentRegisterCommand::class);

        $commandReflection = new \ReflectionClass($this->command);
        $this->execute = $commandReflection->getMethod('execute');
        $this->execute->setAccessible(true);
    }

    /**
     * @test
     */
    public function executePositive(): void
    {
        $outputMock = $this->createMock(OutputInterface::class);
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->method('getOption')->willReturnMap([
            ['shipping-provider', 'ups'],
            ['order', '{"order_id": 11123234,"country":"LT","street":"addresstest","city":"Vilnius","post_code":77777}']
        ]);
        $result = $this->execute->invoke($this->command, $inputMock, $outputMock);

        $this->assertEquals(Command::SUCCESS, $result);
    }

    /**
     * @test
     */
    public function executeExceptionFailure(): void
    {
        $outputMock = $this->createMock(OutputInterface::class);
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->method('getOption')->willReturnMap([
            ['shipping-provider', 'ups'],
            ['order', '{"order_id": 11123234,"country":"LT","street":"addresstest","city":"Vilnius","zip_code":77777}']
        ]);
        $result = $this->execute->invoke($this->command, $inputMock, $outputMock);

        $this->assertEquals(Command::FAILURE, $result);
    }

    /**
     * @test
     */
    public function executeValidationFailure(): void
    {
        $outputMock = $this->createMock(OutputInterface::class);
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->method('getOption')->willReturnMap([
            ['shipping-provider', 'AzerothWyvernDelivery'],
            ['order', '{"order_id": 11123234,"country":"LT","street":"addresstest","city":"Vilnius","zip_code":77777}']
        ]);
        $result = $this->execute->invoke($this->command, $inputMock, $outputMock);

        $this->assertEquals(Command::FAILURE, $result);
    }

}