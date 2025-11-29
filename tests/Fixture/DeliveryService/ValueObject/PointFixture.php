<?php

namespace App\Tests\Fixture\DeliveryService\ValueObject;

use App\Domain\DeliveryService\ValueObject\Point;

final class PointFixture
{
    public static function getOne(
        float $latitude,
        float $longitude
    ): Point {
        return new Point($latitude, $longitude);
    }
}
