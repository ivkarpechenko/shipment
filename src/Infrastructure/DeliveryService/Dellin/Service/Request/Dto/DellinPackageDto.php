<?php

namespace App\Infrastructure\DeliveryService\Dellin\Service\Request\Dto;

use App\Domain\Shipment\Entity\Package;
use Symfony\Component\Uid\Uuid;

readonly class DellinPackageDto
{
    public function __construct(
        public Uuid $id,
        public float $price,
        public float $width,
        public float $height,
        public float $length,
        public float $weight
    ) {
    }

    public static function fromPackage(Package $package): DellinPackageDto
    {
        return new self(
            $package->getId(),
            $package->getPrice(),
            self::fromMillimeterToMeter($package->getWidth()),
            self::fromMillimeterToMeter($package->getHeight()),
            self::fromMillimeterToMeter($package->getLength()),
            self::fromGramsToKilograms($package->getWeight())
        );
    }

    private static function fromMillimeterToMeter(int $millimeter): float
    {
        return floatval($millimeter / 1000);
    }

    private static function fromGramsToKilograms(int $grams): float
    {
        return floatval($grams / 1000);
    }
}
