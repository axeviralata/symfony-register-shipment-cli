<?php

declare(strict_types=1);

namespace App\Enums;

enum ShippingProvider: string
{
    case Ups = 'ups';
    case Dhl = 'dhl';
    case Omniva = 'omniva';

    /**
     * Get all values as array
     * @return array
     */
    public static function getAllValues(): array
    {
        return array_column(ShippingProvider::cases(), 'value');
    }
}
