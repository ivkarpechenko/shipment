<?php

namespace App\Tests\Fixture\Shipment;

use App\Domain\Shipment\Entity\Package;
use App\Domain\Shipment\Entity\PackageProduct;
use App\Domain\Shipment\Entity\Product;
use Symfony\Component\Uid\Uuid;

class PackageProductFixture
{
    public static function getOne(
        int $quantity,
        Product $product,
        ?Package $package = null,
    ): PackageProduct {
        $packageProduct = new PackageProduct($quantity);
        $packageProduct->setProduct($product);

        if ($package) {
            $packageProduct->setPackage($package);
        }

        $reflectionClass = new \ReflectionClass(PackageProduct::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($packageProduct, Uuid::v1());

        return $packageProduct;
    }
}
