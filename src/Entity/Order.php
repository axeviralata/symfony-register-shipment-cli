<?php

declare(strict_types=1);

namespace App\Entity;

class Order
{
    public function __construct(
        private int $id,
        private string $street,
        private string $postCode,
        private string $city,
        private string $country,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }


    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getPostCode(): string
    {
        return $this->postCode;
    }

    public function setPostCode(string $postCode): void
    {
        $this->postCode = $postCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }
    
    public function getShippingProviderKey(): string
    {
        return 'ups';
    }
}
