<?php

namespace App\Tests\Fixture\Shipment;

use App\Application\Shipment\Command\Dto\ProductDto;
use App\Application\Shipment\Command\Dto\StoreDto;

class ProductDtoFixture
{
    public static function getOne(
        string $code,
        string $description,
        string $price,
        int $weight,
        int $width,
        int $height,
        int $length,
        int $quantity,
        bool $isFragile,
        bool $isFlammable,
        bool $isCanRotate,
        int $deliveryPeriod,
        ?StoreDto $store
    ): ProductDto {
        return new ProductDto($code, $description, $price, $weight, $width, $height, $length, $quantity, $isFragile, $isFlammable, $isCanRotate, $deliveryPeriod, $store);
    }

    public static function getOneFilled(
        ?string $code = null,
        ?string $description = null,
        ?string $price = null,
        ?int $weight = null,
        ?int $width = null,
        ?int $height = null,
        ?int $length = null,
        ?int $quantity = null,
        ?bool $isFragile = null,
        ?bool $isFlammable = null,
        ?bool $isCanRotate = null,
        ?int $deliveryPeriod = null,
        ?StoreDto $store = null
    ): ProductDto {
        return self::getOne(
            $code ?? 'AA-1234',
            $description ?? 'description',
            $price ?? '100.00',
            $weight ?? 100,
            $width ?? 100,
            $height ?? 100,
            $length ?? 100,
            $quantity ?? 1,
            $isFragile ?? false,
            $isFlammable ?? false,
            $isCanRotate ?? false,
            $deliveryPeriod ?? 0,
            $store ?? StoreDtoFixture::getOne(
                ContactDtoFixture::getOne('test@gmail.com', 'sender', ['+777777777']),
                1,
                1,
                1,
                1,
                false,
                '309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1',
                [
                    StoreScheduleDtoFixture::getOne(1, '10:00:00', '19:00:00'),
                ],
                new \DateTime(),
                new \DateTime(),
                new \DateTime()
            )
        );
    }
}
