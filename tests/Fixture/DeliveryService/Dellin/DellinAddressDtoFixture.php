<?php

namespace App\Tests\Fixture\DeliveryService\Dellin;

use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinAddressDto;
use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinHouseDataDto;

class DellinAddressDtoFixture
{
    public static function getOne(
        string $result,
        string $source,
        ?string $regionKladrId,
        ?string $regionWithType,
        ?string $cityKladrId,
        ?string $cityWithType,
        ?string $streetKladrId,
        ?string $streetWithType,
        ?string $areaKladrId,
        ?string $areaWithType,
        ?string $settlementKladrId,
        ?string $settlementWithType,
        ?string $street,
        ?string $house,
        ?string $block,
        ?string $flat,
        ?string $country,
        DellinHouseDataDto $houseData
    ): DellinAddressDto {
        return new DellinAddressDto(
            $result,
            $source,
            $regionKladrId,
            $regionWithType,
            $cityKladrId,
            $cityWithType,
            $streetKladrId,
            $streetWithType,
            $areaKladrId,
            $areaWithType,
            $settlementKladrId,
            $settlementWithType,
            $street,
            $house,
            $block,
            $flat,
            $country,
            $houseData
        );
    }

    public static function getOneFilled(
        string $result,
        ?string $source = null,
        ?string $regionKladrId = null,
        ?string $regionWithType = null,
        ?string $cityKladrId = null,
        ?string $cityWithType = null,
        ?string $streetKladrId = null,
        ?string $streetWithType = null,
        ?string $areaKladrId = null,
        ?string $areaWithType = null,
        ?string $settlementKladrId = null,
        ?string $settlementWithType = null,
        ?string $street = null,
        ?string $house = null,
        ?string $block = null,
        ?string $flat = null,
        ?string $country = null,
        ?DellinHouseDataDto $houseData = null
    ): DellinAddressDto {
        return self::getOne(
            $result,
            $source ?: '111395, г Москва, р-н Вешняки, ул Юности, д 5',
            $regionKladrId ?: '7700000000000',
            $regionWithType ?: 'г Москва',
            $cityKladrId ?: '7700000000000',
            $cityWithType ?: 'г Москва',
            $streetKladrId ?: '77000000000046500',
            $streetWithType ?: 'ул Юности',
            $areaKladrId,
            $areaWithType,
            $settlementKladrId,
            $settlementWithType,
            $street ?: 'Юности',
            $house ?: '5',
            $block,
            $flat,
            $country ?: 'Россия',
            $house ?: DellinHouseDataDtoFixture::getOneFilled(5),
        );
    }
}
