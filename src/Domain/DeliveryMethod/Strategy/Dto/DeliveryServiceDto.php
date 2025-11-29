<?php

namespace App\Domain\DeliveryMethod\Strategy\Dto;

readonly class DeliveryServiceDto
{
    public function __construct(
        public string $code,
        public string $name
    ) {
    }
}
