<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Order as OrderEntity;
use App\Entity\OrderShipment;

class Order
{
    public function registerShipping(OrderEntity $order)
    {
        //implement this function
    }
    public function createOrderShipment(string $orderJson, ShippingServiceInterface $serviceProvider): OrderShipment
    {
        $orderData = json_decode($orderJson,true);
        if (!key_exists('order_id',$orderData)) {
            throw new \Exception('order_id param is not found in proposed json');
        }
        $order = $this->getOrder($orderData['order_id']);
        return new OrderShipment(
            1,
            $order->getId(),
            1,
            'created'

        );
    }

    private function getOrder(int $id): OrderEntity
    {
        return new OrderEntity(
            $id,
            'street',
            '77777',
            'Vilnius',
            'LT'
        );
    }
}
