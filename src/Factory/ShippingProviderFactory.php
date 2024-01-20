<?php

namespace App\Factory;

use App\Service\DHLShippingService;
use App\Service\OMNIVAShippingService;
use App\Service\ShippingServiceInterface;
use App\Service\UPSShippingService;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ShippingProviderFactory
{
    public static function create(string $shippingProvider): ?ShippingServiceInterface
    {
        //TODO: use as param,for testing
        /* $httpClient = new MockHttpClient([
             new MockResponse('{"data": "mocked_data"}', ['http_code' => 200]),
         ]);*/
        return match ($shippingProvider) {
            'ups' => new UPSShippingService(new MockHttpClient([
                new MockResponse('{"data":{"type":"parcel","attributes":{"parcel_id": 22222,"provider": "ups"}}}', ['http_code' => 200]),
            ])),
            'dhl' => new DHLShippingService(new MockHttpClient([
                new MockResponse('{"data":{"type":"parcel","attributes":{"parcel_id": 22222,"provider": "dhl"}}}', ['http_code' => 200]),
            ])),
            'omniva' => new OMNIVAShippingService(new MockHttpClient([
                new MockResponse('{"data":{"type":"pickup_point","attributes":{"pickup_point_id": 11111}}}', ['http_code' => 200]),
                new MockResponse('{"data":{"type":"parcel","attributes":{"parcel_id": 22222,"provider": "omniva"}}}', ['http_code' => 200])
            ])),
            default => null
        };
    }


}