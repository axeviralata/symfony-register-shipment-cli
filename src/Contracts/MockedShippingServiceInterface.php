<?php

declare(strict_types=1);

namespace App\Contracts;

use Symfony\Component\HttpClient\MockHttpClient;

interface MockedShippingServiceInterface
{
    public function initMockedClient(): MockHttpClient;
}