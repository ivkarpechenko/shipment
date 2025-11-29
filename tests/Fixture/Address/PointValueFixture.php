<?php

namespace App\Tests\Fixture\Address;

use App\Domain\Address\ValueObject\Point;

class PointValueFixture
{
    public static function getOne(float $latitude, float $longitude): Point
    {
        return new Point($latitude, $longitude);
    }
}
