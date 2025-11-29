<?php

namespace App\Tests\Fixture\Shipment;

use App\Domain\Shipment\Entity\Package;
use Symfony\Component\Uid\Uuid;

class PackageFixture
{
    public static function getOne(
        float $price,
        int $width,
        int $height,
        int $length,
        int $weight
    ): Package {
        $package = new Package($price, $width, $height, $length, $weight);

        $reflectionClass = new \ReflectionClass(Package::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($package, Uuid::v1());

        return $package;
    }

    public static function getOneFilled(
        ?float $price = null,
        ?int $width = null,
        ?int $height = null,
        ?int $length = null,
        ?int $weight = null,
        array $products = []
    ): Package {
        $package = new Package(
            $price ?? 100.0,
            $width ?? 10,
            $height ?? 10,
            $length ?? 10,
            $weight ?? 10
        );

        foreach ($products as $product) {
            $package->addProduct($product);
        }

        $reflectionClass = new \ReflectionClass(Package::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($package, Uuid::v1());

        return $package;
    }
}
