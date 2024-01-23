<?php

declare(strict_types=1);

namespace App\Tests\Unit\Enums;

use App\Enums\ShippingProvider;
use PHPUnit\Framework\TestCase;

class ShippingProviderTest extends TestCase
{
    /**
     * @test
     */
    public function allEnumValuesCheck(): void
    {
        $this->assertEquals(['ups', 'dhl', 'omniva'], ShippingProvider::getAllValues());
    }
}