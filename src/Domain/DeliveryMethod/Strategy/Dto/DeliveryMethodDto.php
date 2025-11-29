<?php

namespace App\Domain\DeliveryMethod\Strategy\Dto;

readonly class DeliveryMethodDto
{
    public function __construct(
        public string $code,
        public string $name,
        /** @var DeliveryServiceDto[] $deliveryServices */
        public array $deliveryServices
    ) {
    }
}
