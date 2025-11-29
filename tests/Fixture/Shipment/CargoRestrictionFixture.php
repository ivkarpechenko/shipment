<?php

declare(strict_types=1);

namespace App\Tests\Fixture\Shipment;

use App\Domain\Shipment\Entity\CargoRestriction;
use App\Domain\Shipment\Entity\CargoType;
use App\Domain\Shipment\Entity\Shipment;
use Symfony\Component\Uid\Uuid;

class CargoRestrictionFixture
{
    public static function getOne(
        CargoType $cargoType,
        Shipment $shipment,
        int $maxWidth,
        int $maxHeight,
        int $maxLength,
        int $maxWeight,
        int $maxVolume,
        int $maxSumDimensions,
        ?Uuid $id = null
    ): CargoRestriction {
        $cargoRestriction = new CargoRestriction($cargoType, $shipment, $maxWidth, $maxHeight, $maxLength, $maxWeight, $maxVolume, $maxSumDimensions);

        $reflectionClass = new \ReflectionClass(CargoRestriction::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($cargoRestriction, $id ?: Uuid::v1());

        return $cargoRestriction;
    }

    public static function getOneFilled(
        ?CargoType $cargoType = null,
        ?Shipment $shipment = null,
        ?int $maxWidth = null,
        ?int $maxHeight = null,
        ?int $maxLength = null,
        ?int $maxWeight = null,
        ?int $maxVolume = null,
        ?int $maxSumDimensions = null,
        ?Uuid $id = null
    ): CargoRestriction {
        $cargoRestriction = new CargoRestriction(
            cargoType: $cargoType ?: CargoTypeFixture::getOne('code', 'name'),
            shipment: $shipment ?: ShipmentFixture::getOneFilled(),
            maxWidth: $maxWidth ?: 100,
            maxHeight: $maxHeight ?: 200,
            maxLength: $maxLength ?: 300,
            maxWeight: $maxWeight ?: 400,
            maxVolume: $maxVolume ?: 500,
            maxSumDimensions: $maxSumDimensions ?: 600,
        );

        $reflectionClass = new \ReflectionClass(CargoRestriction::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($cargoRestriction, $id ?: Uuid::v1());

        return $cargoRestriction;
    }
}
