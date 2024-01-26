<?php

declare(strict_types=1);

namespace App\Validator;

use App\Contracts\OrderShipmentDTOInterface;
use App\Contracts\OrderShipmentDTOValidatorInterface;
use InvalidArgumentException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderShipmentDTOValidator implements OrderShipmentDTOValidatorInterface
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * @param OrderShipmentDTOInterface $shipmentDTO
     * @return void
     * @throws \Exception
     */
    public function validateDTO(OrderShipmentDTOInterface $shipmentDTO): void
    {
        $violations = $this->validator->validate($shipmentDTO);
        if (0 !== count($violations)) {
            foreach ($violations as $violation) {
                $result[] = sprintf(
                    'Order json attribute "%s": %s',
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
            }
            throw new InvalidArgumentException(implode(PHP_EOL, $result));
        }
    }

}