<?php

namespace App\Tests\Fixture\Shipment;

use App\Application\Shipment\Command\Dto\PackageDto;

class PackageDtoFixture
{
    public static function getOne(float $price, int $width, int $height, int $length, int $weight): PackageDto
    {
        return new PackageDto($price, $width, $height, $length, $weight);
    }
}
