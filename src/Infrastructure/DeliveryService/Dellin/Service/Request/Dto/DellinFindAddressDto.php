<?php

namespace App\Infrastructure\DeliveryService\Dellin\Service\Request\Dto;

readonly class DellinFindAddressDto
{
    public function __construct(
        public array $data,
        public string $type = 'address',
        public string $mode = 'pretty'
    ) {
    }

    public static function from(array $data, string $type = 'address', string $mode = 'pretty'): DellinFindAddressDto
    {
        return new self($data, $type, $mode);
    }
}
