<?php

namespace App\Tests\Fixture\Shipment;

use App\Domain\Shipment\Entity\Product;
use App\Domain\Shipment\Entity\Store;
use Symfony\Component\Uid\Uuid;

class ProductFixture
{
    public static function getOne(
        string $code,
        string $description,
        string $price,
        float $weight,
        int $width,
        int $height,
        int $length,
        int $quantity,
        bool $isFragile,
        bool $isFlammable,
        bool $isCanRotate,
        int $deliveryPeriod
    ): Product {
        $product = new Product($code, $description, $price, $weight, $width, $height, $length, $quantity, $isFragile, $isFlammable, $isCanRotate, $deliveryPeriod);

        $reflectionClass = new \ReflectionClass(Product::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($product, Uuid::v1());

        return $product;
    }

    public static function getOneFilled(
        ?string $code = null,
        ?string $description = null,
        ?string $price = null,
        ?float $weight = null,
        ?int $width = null,
        ?int $height = null,
        ?int $length = null,
        ?int $quantity = null,
        ?bool $isFragile = null,
        ?bool $isFlammable = null,
        ?bool $isCanRotate = null,
        ?int $deliveryPeriod = null,
        ?Store $store = null
    ): Product {
        $product = new Product(
            $code ?? 'AA-1234',
            $description ?? 'desc',
            $price ?? '100.0',
            $weight ?? 10.0,
            $width ?? 10,
            $height ?? 10,
            $length ?? 10,
            $quantity ?? 1,
            $isFragile ?? false,
            $isFlammable ?? false,
            $isCanRotate ?? false,
            $deliveryPeriod ?? 10
        );

        $product->setStore($store ?? StoreFixture::getOneFilled());

        $reflectionClass = new \ReflectionClass(Product::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($product, Uuid::v1());

        return $product;
    }
}
