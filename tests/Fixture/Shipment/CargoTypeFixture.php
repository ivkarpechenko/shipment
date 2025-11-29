<?php

declare(strict_types=1);

namespace App\Tests\Fixture\Shipment;

use App\Domain\Shipment\Entity\CargoType;
use Symfony\Component\Uid\Uuid;

class CargoTypeFixture
{
    public static function getOne(string $code, string $name, ?Uuid $id = null): CargoType
    {
        $cargoType = new CargoType($code, $name);

        $reflectionClass = new \ReflectionClass(CargoType::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($cargoType, $id ?: Uuid::v1());

        return $cargoType;
    }
}
