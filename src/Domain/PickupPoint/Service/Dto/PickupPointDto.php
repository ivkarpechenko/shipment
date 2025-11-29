<?php

namespace App\Domain\PickupPoint\Service\Dto;

use App\Domain\Address\ValueObject\Point;
use App\Domain\DeliveryService\Entity\DeliveryService;

readonly class PickupPointDto
{
    public function __construct(
        public string $name,
        public DeliveryService $deliveryService,
        public array $phones,
        public Point $point,
        public string $address,
        public string $workTime,
        public string $code,
        public string $type,
        public ?float $weightMin,
        public ?float $weightMax,
        public ?float $width,
        public ?float $height,
        public ?float $depth,
    ) {
    }
}
