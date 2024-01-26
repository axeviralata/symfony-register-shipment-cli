<?php

declare(strict_types=1);

namespace App\Service;

use App\Factory\ShippingServiceFactory;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class Shipment
{
    public function __construct(
        private readonly ShippingServiceFactory $shippingServiceFactory,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Process orderJson with one of existing shipping service providers
     * @param string $orderJson
     * @param string $provider
     * @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function process(string $orderJson, string $provider): void
    {
        $shipmentServiceProvider = $this->shippingServiceFactory->create($provider);
        $shipmentServiceProvider->initMockedClient();
        $shipmentDTO = $shipmentServiceProvider->createDTO($orderJson);
        $this->logger->info('Processing of Order Shipment', ['order_id' => $shipmentDTO->getOrderId()]);
        try {
            $response = $shipmentServiceProvider->register($shipmentDTO);
            $statusCode = $response->getStatusCode();
            if ($statusCode >= Response::HTTP_OK && $statusCode <= Response::HTTP_IM_USED) {
                $this->logger->info(
                    '-- Shipment was successfully processed',
                    ['order_id' => $shipmentDTO->getOrderId()]
                );
            } else {
                $this->logger->error(
                    '-- Registration endpoint returned not acceptable status code.',
                    ['response_status_code' => $statusCode]
                );
            }
        } catch (Exception $exception) {
            $this->logger->error(
                'Exception thrown during shipment register',
                ['error' => $exception->getMessage()]
            );
            throw new \RuntimeException($exception->getMessage());
        }
    }
}