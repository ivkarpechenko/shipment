<?php

namespace App\Domain\Address\ValueObject;

readonly class Point
{
    public function __construct(
        private ?float $latitude,
        private ?float $longitude,
    ) {
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function toWKT(): string
    {
        return sprintf('POINT(%f %f)', $this->longitude, $this->latitude);
    }
}
