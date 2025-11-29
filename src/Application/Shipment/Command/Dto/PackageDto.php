<?php

namespace App\Application\Shipment\Command\Dto;

final class PackageDto
{
    public function __construct(
        public float $price,
        public int $width,
        public int $height,
        public int $length,
        public int $weight,
        /** @var ProductDto[] $products */
        public array $products = [],
    ) {
    }

    public static function fromArray(array $package): self
    {
        return new self(
            (float) $package['price'],
            (int) $package['width'],
            (int) $package['height'],
            (int) $package['length'],
            (int) $package['weight'],
            array_map(function (array $product) {
                return ProductDto::fromArray($product);
            }, $package['products'] ?? []),
        );
    }
}
