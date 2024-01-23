<?php

declare(strict_types=1);

namespace App\Tests\Helpers;

use Symfony\Component\Serializer\SerializerInterface;

class SerializerHelper implements SerializerInterface
{
    /**
     * Only for mock purpose
     * @param mixed $data
     * @param string $format
     * @param array $context
     * @return string
     */
    public function serialize(mixed $data, string $format, array $context = []): string
    {
        return 'string';
    }

    /**
     * Only for mock purpose
     * @param mixed $data
     * @param string $type
     * @param string $format
     * @param array $context
     * @return mixed
     */
    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        return [];
    }
}