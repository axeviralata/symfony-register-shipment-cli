<?php

namespace App\Service;

use App\Entity\Order as OrderEntity;
use App\Entity\OrderShipment;
use App\Factory\ShippingProviderFactory;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

class Shipment
{
    private LoggerInterface $logger;

    #[Required]
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Process orderShipment with one of existing shipping service providers
     * @param OrderShipment $shipment
     * @param ShippingServiceInterface $service
     * @return void
     */
    public function register(OrderShipment $shipment, ShippingServiceInterface $service)
    {
        $response = $service->register($shipment);
        $this->logger->info('Processing of shipment id ' . $shipment->id . ' related to Order id ' . $shipment->orderId);
        if (in_array($response->getStatusCode(), [Response::HTTP_OK, Response::HTTP_CREATED])) {
            $this->logger->info('-- Shipment id ' . $shipment->id . ' was processed');
        } else {
            $this->logger->error('--Smth happened during registration of shipping');
        }
    }

}