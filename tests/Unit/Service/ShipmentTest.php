<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\DTO\DHLOrderShipment;
use App\Factory\ShippingServiceFactory;
use App\Service\DHLShippingService;
use App\Service\Shipment;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Contracts\HttpClient\ResponseInterface;


class ShipmentTest extends TestCase
{
    public function setUP(): void
    {
    }

    /**
     * @test
     */
    public function processPositive(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->exactly(2))
            ->method('info')
            ->withConsecutive(
                ['Processing of Order Shipment', ['order_id' => 777]],
                ['-- Shipment was successfully processed']
            );

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_OK);

        $dto = new DHLOrderShipment();
        $dto->setOrderId(777);

        $service = $this->createMock(DHLShippingService::class);
        $service->expects($this->once())
            ->method('initMockedClient')
            ->willReturn(new MockHttpClient());
        $service->expects($this->once())
            ->method('register')
            ->willReturn($response);
        $service->expects($this->once())
            ->method('createDTO')
            ->willReturn($dto);

        $factory = $this->createMock(ShippingServiceFactory::class);
        $factory->expects($this->once())
            ->method('create')
            ->willReturn($service);

        $shipment = new Shipment($factory, $loggerMock);
        $shipment->process('{"iam":"groot"}', 'dhl');
    }

    /**
     * @test
     */
    public function processNegative(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('info')
            ->with('Processing of Order Shipment', ['order_id' => 777]);
        $loggerMock->expects($this->once())
            ->method('error')
            ->with('-- Registration endpoint returned not acceptable status code.', ['response_status_code' => 400]);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_BAD_REQUEST);

        $dto = new DHLOrderShipment();
        $dto->setOrderId(777);

        $service = $this->createMock(DHLShippingService::class);
        $service->expects($this->once())
            ->method('initMockedClient')
            ->willReturn(new MockHttpClient());
        $service->expects($this->once())
            ->method('register')
            ->willReturn($response);
        $service->expects($this->once())
            ->method('createDTO')
            ->willReturn($dto);

        $factory = $this->createMock(ShippingServiceFactory::class);
        $factory->expects($this->once())
            ->method('create')
            ->willReturn($service);

        $shipment = new Shipment($factory, $loggerMock);
        $shipment->process('{"iam":"groot"}', 'dhl');
    }

    /**
     * @test
     */
    public function processWithException(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('info')
            ->with('Processing of Order Shipment', ['order_id' => 777]);
        $loggerMock->expects($this->once())
            ->method('error')
            ->with('Exception thrown during shipment register', ['error' => 'Ops!']);

        $dto = new DHLOrderShipment();
        $dto->setOrderId(777);

        $service = $this->createMock(DHLShippingService::class);
        $service->expects($this->once())
            ->method('initMockedClient')
            ->willReturn(new MockHttpClient());
        $service->expects($this->once())
            ->method('register')
            ->willReturnCallback(function () {
                throw new \Exception('Ops!');
            });
        $service->expects($this->once())
            ->method('createDTO')
            ->willReturn($dto);

        $factory = $this->createMock(ShippingServiceFactory::class);
        $factory->expects($this->once())
            ->method('create')
            ->willReturn($service);

        $shipment = new Shipment($factory, $loggerMock);
        $this->expectException(\Exception::class);
        $shipment->process('{"iam":"groot"}', 'dhl');
    }
}