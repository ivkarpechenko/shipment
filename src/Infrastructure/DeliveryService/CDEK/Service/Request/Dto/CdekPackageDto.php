<?php

namespace App\Infrastructure\DeliveryService\CDEK\Service\Request\Dto;

use App\Domain\Shipment\Entity\Package;
use Symfony\Component\Uid\Uuid;

readonly class CdekPackageDto
{
    public function __construct(
        public Uuid $id,
        public float $price,
        public int $width,
        public int $height,
        public int $length,
        public int $weight
    ) {
    }

    public static function fromPackage(Package $package): CdekPackageDto
    {
        return new self(
            $package->getId(),
            $package->getPrice(),
            self::fromMillimeterToCentimeter($package->getWidth()),
            self::fromMillimeterToCentimeter($package->getHeight()),
            self::fromMillimeterToCentimeter($package->getLength()),
            $package->getWeight()
        );
    }

    private static function fromMillimeterToCentimeter(int $millimeter): int
    {
        return intval(ceil($millimeter / 10));
    }
}
