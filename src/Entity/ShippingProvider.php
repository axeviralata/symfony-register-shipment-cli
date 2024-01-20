<?php

namespace App\Entity;

class ShippingProvider
{
    public function __construct(
        public int    $id,
        public string $name,
        public bool   $enabled
    )
    {
    }

    /**
     * List of the available shipping providers configured and enabled in the app
     * @return array
     */
    public static function getAllEnabled()
    {
        return ['ups', 'dhl', 'omniva'];
    }
}