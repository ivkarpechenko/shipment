<?php

namespace App\Domain\DeliveryService\ValueObject;

use App\Domain\DeliveryService\Exception\DeliveryServiceRestrictAreaPolygonCoordinatesEmptyException;
use App\Domain\DeliveryService\Exception\DeliveryServiceRestrictAreaPolygonCoordinatesInvalidFormatException;

readonly class Polygon
{
    public function __construct(
        /** @var Point[] */
        private array $coordinates
    ) {
        $this->validate($this->coordinates);
    }

    public function getCoordinates(): array
    {
        return $this->coordinates;
    }

    public function isEqual(self $polygon): bool
    {
        return $polygon->getCoordinates() === $this->coordinates;
    }

    protected function validate(array $coordinates): void
    {
        if (empty($coordinates)) {
            throw new DeliveryServiceRestrictAreaPolygonCoordinatesEmptyException('Polygon coordinates cannot be empty');
        }

        $coordinates = array_filter($coordinates, function ($lines) {
            return array_filter($lines, function ($point) {
                return $point instanceof Point;
            });
        });

        if (empty($coordinates)) {
            throw new DeliveryServiceRestrictAreaPolygonCoordinatesInvalidFormatException('Polygon coordinates invalid format');
        }
    }
}
