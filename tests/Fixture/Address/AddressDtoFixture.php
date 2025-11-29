<?php

namespace App\Tests\Fixture\Address;

use App\Domain\Address\Service\Dto\AddressDto;

class AddressDtoFixture
{
    public static function getOne(
        string $address,
        string $house,
        string $country,
        string $countryIsoCode,
        string $region,
        string $regionIsoCode,
        string $city,
        string $cityType,
        ?float $latitude,
        ?float $longitude,
        ?string $postalCode,
        ?string $street,
        ?string $flat,
        ?string $entrance,
        ?string $floor,
        ?string $settlement,
        ?array $inputData = []
    ): AddressDto {
        return new AddressDto(
            $address,
            $house,
            $country,
            $countryIsoCode,
            $region,
            $regionIsoCode,
            $city,
            $cityType,
            $latitude,
            $longitude,
            $postalCode,
            $street,
            $flat,
            $entrance,
            $floor,
            $settlement,
            $inputData
        );
    }

    public static function getOneFilled(
        ?string $address = null,
        ?string $house = null,
        ?string $country = null,
        ?string $countryIsoCode = null,
        ?string $region = null,
        ?string $regionIsoCode = null,
        ?string $city = null,
        ?string $cityType = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?string $postalCode = null,
        ?string $street = null,
        ?string $flat = null,
        ?string $entrance = null,
        ?string $floor = null,
        ?string $settlement = null,
        ?array $inputData = []
    ): AddressDto {
        return new AddressDto(
            $address ?: '309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1',
            $house ?: '1/1',
            $country ?: 'Россия',
            $countryIsoCode ?: 'RU',
            $region ?: 'Белгородская',
            $regionIsoCode ?: 'RU-BEL',
            $city ?: 'Алексеевка',
            $cityType ?: 'город',
            $latitude ?: 42.4324,
            $longitude ?: 43.4234,
            $postalCode ?: '309850',
            $street ?: 'ул Слободская',
            $flat ?: '',
            $entrance,
            $floor,
            $settlement,
            $inputData
        );
    }
}
