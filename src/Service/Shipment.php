<?php

declare(strict_types=1);

namespace App\Service;

use App\Contracts\ShippingServiceInterface;
use App\Validator\OrderShipmentDTOValidator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class Shipment
{
    public function __construct(
        private SerializerInterface $serializer,
        private OrderShipmentDTOValidator $validator,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Process orderJson with one of existing shipping service providers
     * @param string $orderJson
     * @param ShippingServiceInterface $service
     * @return void
     * @throws \Exception
     */
    public function process(string $orderJson, ShippingServiceInterface $service): void
    {
        $shipmentDTO = $this->serializer->deserialize($orderJson, $service->getDTOClass(), 'json');
        $this->validator->validateDTO($shipmentDTO);
        $service->initMockedClient();
        $this->logger->info('Processing of shipment related to Order id = ' . $shipmentDTO->getOrderId());
        try {
            $response = $service->register($shipmentDTO);
            $statusCode = $response->getStatusCode();
            if ($statusCode >= Response::HTTP_OK && $statusCode <= Response::HTTP_IM_USED) {
                $this->logger->info('-- Shipment was successfully processed');
            } else {
                $this->logger->error('-- Registration endpoint returned status code: ' . $statusCode);
            }
        } catch (\Exception $exception) {
            throw new \RuntimeException($exception->getMessage());
        }
    }
}