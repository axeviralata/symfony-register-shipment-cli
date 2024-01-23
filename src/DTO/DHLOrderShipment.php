<?php
declare(strict_types=1);

namespace App\DTO;

use App\Contracts\OrderShipmentDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;


class DHLOrderShipment implements OrderShipmentDTOInterface
{
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    private ?int $order_id = 1;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private ?string $country = null;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private ?string $address = null;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private ?string $town = null;

    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    private ?int $zip_code = null;

    /**
     * @return int|null
     */
    public function getOrderId(): ?int
    {
        return $this->order_id;
    }

    /**
     * @param int|null $order_id
     */
    public function setOrderId(?int $order_id): void
    {
        $this->order_id = $order_id;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string|null $country
     */
    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getTown(): ?string
    {
        return $this->town;
    }

    /**
     * @param string|null $town
     */
    public function setTown(?string $town): void
    {
        $this->town = $town;
    }

    /**
     * @return int|null
     */
    public function getZipCode(): ?int
    {
        return $this->zip_code;
    }

    /**
     * @param int|null $zip_code
     */
    public function setZipCode(?int $zip_code): void
    {
        $this->zip_code = $zip_code;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}