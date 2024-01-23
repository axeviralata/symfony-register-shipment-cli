<?php

declare(strict_types=1);

namespace App\DTO;

use App\Contracts\OrderShipmentDTOInterface;
use Symfony\Component\Validator\Constraints as Assert;


class OMNIVAOrderShipment implements OrderShipmentDTOInterface
{
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    private ?int $order_id = 1;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private ?string $country = null;

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