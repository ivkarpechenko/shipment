<?php

declare(strict_types=1);

namespace App\Application\Shipment\Command\Dto;

readonly class CargoRestrictionDto
{
    public function __construct(
        public string $code,
        public int $maxWidth,
        public int $maxHeight,
        public int $maxLength,
        public int $maxWeight,
        public int $maxVolume,
        public int $maxSumDimensions,
    ) {
    }

    public static function fromArray(array $cargoRestriction): CargoRestrictionDto
    {
        return new self(
            $cargoRestriction['code'],
            (int) $cargoRestriction['maxWidth'],
            (int) $cargoRestriction['maxHeight'],
            (int) $cargoRestriction['maxLength'],
            (int) $cargoRestriction['maxWeight'],
            (int) $cargoRestriction['maxVolume'],
            (int) $cargoRestriction['maxSumDimensions'],
        );
    }
}
