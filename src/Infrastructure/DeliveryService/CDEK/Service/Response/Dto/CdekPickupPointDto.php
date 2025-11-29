<?php

namespace App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto;

readonly class CdekPickupPointDto
{
    public function __construct(
        public string $workTime,
        public string $code,
        public string $type,
        public ?float $weightMin,
        public ?float $weightMax,
        public string $address,
        public ?float $latitude,
        public ?float $longitude,
        public ?float $width,
        public ?float $height,
        public ?float $depth,
        public array $phones
    ) {
    }
}
