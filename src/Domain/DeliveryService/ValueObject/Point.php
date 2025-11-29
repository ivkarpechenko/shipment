<?php

namespace App\Domain\DeliveryService\ValueObject;

use App\Domain\DeliveryService\Exception\LatitudeInvalidFormatException;
use App\Domain\DeliveryService\Exception\LongitudeInvalidFormatException;

readonly class Point
{
    private float $latitude;

    private float $longitude;

    public function __construct(float $latitude, float $longitude)
    {
        $this->validateLatitude($latitude);
        $this->validateLongitude($longitude);

        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    protected function validateLatitude(float $latitude): void
    {
        $validate = preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/', $latitude);
        if (!$validate) {
            throw new LatitudeInvalidFormatException(sprintf('Latitude %f invalid format', $latitude));
        }
    }

    protected function validateLongitude(float $longitude): void
    {
        $validate = preg_match('/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $longitude);

        if (!$validate) {
            throw new LongitudeInvalidFormatException(sprintf('Longitude %f invalid format', $longitude));
        }
    }
}
