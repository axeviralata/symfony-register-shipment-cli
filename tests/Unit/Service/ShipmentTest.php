<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Contracts\OrderShipmentDTOInterface;
use App\Service\DHLShippingService;
use App\Service\Shipment;
use App\Tests\Helpers\SerializerHelper;
use App\Validator\OrderShipmentDTOValidator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Contracts\HttpClient\ResponseInterface;


class ShipmentTest extends TestCase
{
    private $serializer;
    private $validator;

    public function setUP(): void
    {
        $dto = $this->createMock(OrderShipmentDTOInterface::class);
        $dto->expects($this->once())
            ->method('getOrderId')
            ->willReturn(777);

        $this->serializer = $this->createMock(SerializerHelper::class);
        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->willReturn($dto);

        $this->validator = $this->createMock(OrderShipmentDTOValidator::class);
        $this->validator->expects($this->once())
            ->method('validateDTO')
            ->willReturnCallback(function () {
            });
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
                ['Processing of shipment related to Order id = 777'],
                ['-- Shipment was successfully processed']
            );
        $shipment = new Shipment($this->serializer, $this->validator, $loggerMock);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_OK);

        $service = $this->createMock(DHLShippingService::class);
        $service->expects($this->once())
            ->method('initMockedClient')
            ->willReturn(new MockHttpClient());
        $service->expects($this->once())
            ->method('register')
            ->willReturn($response);

        $shipment->process('{"iam":"groot"}', $service);
    }

    /**
     * @test
     */
    public function processNegative(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('info')
            ->with('Processing of shipment related to Order id = 777');
        $loggerMock->expects($this->once())
            ->method('error')
            ->with('-- Registration endpoint returned status code: 400');
        $shipment = new Shipment($this->serializer, $this->validator, $loggerMock);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_BAD_REQUEST);

        $service = $this->createMock(DHLShippingService::class);
        $service->expects($this->once())
            ->method('initMockedClient')
            ->willReturn(new MockHttpClient());
        $service->expects($this->once())
            ->method('register')
            ->willReturn($response);

        $shipment->process('{"iam":"groot"}', $service);
    }

    /**
     * @test
     */
    public function processWithException(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('info')
            ->with('Processing of shipment related to Order id = 777');

        $shipment = new Shipment($this->serializer, $this->validator, $loggerMock);

        $service = $this->createMock(DHLShippingService::class);
        $service->expects($this->once())
            ->method('initMockedClient')
            ->willReturn(new MockHttpClient());
        $service->expects($this->once())
            ->method('register')
            ->willReturnCallback(function () {
                throw new \Exception('Ops!');
            });
        $this->expectException(\Exception::class);
        $shipment->process('{"iam":"groot"}', $service);
    }
}