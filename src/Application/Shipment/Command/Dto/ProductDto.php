<?php

namespace App\Application\Shipment\Command\Dto;

final class ProductDto
{
    public function __construct(
        public string $code,
        public string $description,
        public string $price,
        public int $weight,
        public int $width,
        public int $height,
        public int $length,
        public int $quantity,
        public bool $isFragile,
        public bool $isFlammable,
        public bool $isCanRotate,
        public int $deliveryPeriod,
        public ?StoreDto $store
    ) {
    }

    public static function fromArray(array $product): self
    {
        return new self(
            (string) $product['code'],
            (string) $product['description'],
            (string) $product['price'],
            array_key_exists('weight', $product) ? $product['weight'] : 0,
            array_key_exists('width', $product) ? $product['width'] : 0,
            array_key_exists('height', $product) ? $product['height'] : 0,
            array_key_exists('length', $product) ? $product['length'] : 0,
            (int) $product['quantity'],
            array_key_exists('isFragile', $product) ? $product['isFragile'] : false,
            array_key_exists('isFlammable', $product) ? $product['isFlammable'] : false,
            array_key_exists('isCanRotate', $product) ? $product['isCanRotate'] : false,
            array_key_exists('deliveryPeriod', $product) ? $product['deliveryPeriod'] : 0,
            array_key_exists('store', $product)
                ? StoreDto::fromArray($product['store'])
                : null
        );
    }
}
