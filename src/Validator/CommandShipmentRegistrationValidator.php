<?php

declare(strict_types=1);

namespace App\Validator;

use App\Contracts\CLIInputValidatorInterface;
use App\Enums\ShippingProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommandShipmentRegistrationValidator implements CLIInputValidatorInterface
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * Validation of both params :
     * 1. shippimg-provider - checks supported providers
     * 2. order - checks if is json, if order_id is not empty and integer
     * @param InputInterface $input
     * @return array
     */
    public function validateInput(InputInterface $input): array
    {
        $provider = $input->getOption('shipping-provider');
        $providerViolations = $this->validator->validate(strtolower($provider), [
            new Choice(['callback' => [ShippingProvider::class, 'getAllValues']])
        ]);

        $orderDataViolations = $this->validator->validate($input->getOption('order'), [new Json()]);
        $result = array_merge(
            $this->buildResponseArray($providerViolations),
            $this->buildResponseArray($orderDataViolations)
        );

        return $result;
    }

    /**
     * @param ConstraintViolationListInterface $violations
     * @return array
     */
    public function buildResponseArray(ConstraintViolationListInterface $violations): array
    {
        if (0 !== count($violations)) {
            foreach ($violations as $violation) {
                $result[] = $violation->getInvalidValue() . ': ' . $violation->getMessage();
            }
            return $result;
        }
        return [];
    }


}