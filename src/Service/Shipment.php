<?php
declare(strict_types=1);

namespace App\Service;

use App\Contracts\OrderShipmentDTOInterface;
use App\Contracts\ShippingServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Shipment
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface  $validator,
        private LoggerInterface     $logger
    )
    {
    }

    /**
     * Process orderJson with one of existing shipping service providers
     * @param string $orderJson
     * @param ShippingServiceInterface $service
     * @return void
     * @throws \Exception
     */
    public function process(string $orderJson, ShippingServiceInterface $service)
    {
        $shipmentDTO = $this->serializer->deserialize($orderJson, $service->getDTOClass(), 'json');
        $this->validateDTO($shipmentDTO);
        $service->initMockedClient();
        $this->logger->info('Processing of shipment related to Order id =' . $shipmentDTO->getOrderId());
        $response = $service->register($shipmentDTO);
        if (in_array($response->getStatusCode(), [Response::HTTP_OK, Response::HTTP_CREATED])) {
            $this->logger->info('-- Shipment was successfully processed');
        } else {
            $this->logger->error('--Smth happened during registration of shipping');
        }
    }

    /**
     * Validate DTO before request
     * @param OrderShipmentDTOInterface $shipmentDTO
     * @return void
     * @throws \Exception
     */
    public function validateDTO(OrderShipmentDTOInterface $shipmentDTO)
    {
        $violations = $this->validator->validate($shipmentDTO);
        if (0 !== count($violations)) {
            foreach ($violations as $violation) {
                $result[] = $violation->getPropertyPath() . ': ' . $violation->getMessage() . PHP_EOL;
            }
            throw new \Exception(implode(' ', $result));
        }
    }
}