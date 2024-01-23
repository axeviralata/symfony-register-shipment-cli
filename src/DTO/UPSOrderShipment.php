<?php
declare(strict_types=1);

namespace App\DTO;

use App\Contracts\OrderShipmentDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;


class UPSOrderShipment implements OrderShipmentDTOInterface
{
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    private ?int $order_id = 1;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private ?string $country = null;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private ?string $street = null;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private ?string $city = null;

    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    private ?int $post_code = null;

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
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string|null $street
     */
    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return int|null
     */
    public function getPostCode(): ?int
    {
        return $this->post_code;
    }

    /**
     * @param int|null $post_code
     */
    public function setPostCode(?int $post_code): void
    {
        $this->post_code = $post_code;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}