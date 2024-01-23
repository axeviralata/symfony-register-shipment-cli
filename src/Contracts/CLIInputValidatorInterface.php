<?php

declare(strict_types=1);

namespace App\Contracts;

use Symfony\Component\Console\Input\InputInterface;

interface CLIInputValidatorInterface
{
    public function validateInput(InputInterface $input): array;
}