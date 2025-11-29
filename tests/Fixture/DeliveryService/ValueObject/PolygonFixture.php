<?php

namespace App\Tests\Fixture\DeliveryService\ValueObject;

use App\Domain\DeliveryService\ValueObject\Point;
use App\Domain\DeliveryService\ValueObject\Polygon;

final class PolygonFixture
{
    /** @param Point[] $coordinates */
    public static function getOne(array $coordinates): Polygon
    {
        return new Polygon($coordinates);
    }
}
