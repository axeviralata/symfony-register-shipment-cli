<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldHaveUpsAsDefaultShipping(): void
    {
        $order = new Order(1, 'street', '20', 'Malmoe', 'Sweden');

        $this->assertEquals('ups', $order->getShippingProviderKey());
    }
}
