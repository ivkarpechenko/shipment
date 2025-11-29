<?php

namespace App\Tests\Fixture\Address;

use App\Domain\Address\Entity\Address;
use App\Domain\Address\Service\Dto\AddressDto;
use App\Domain\Address\ValueObject\Point;
use App\Domain\City\Entity\City;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Component\Uid\Uuid;

final class AddressFixture
{
    public static function getOne(
        City $city,
        string $address,
        string $house,
        ?Point $point,
        ?string $postalCode = null,
        ?string $street = null,
        ?string $flat = null,
        ?string $entrance = null,
        ?string $floor = null,
        ?string $settlement = null,
        ?array $inputData = [],
        ?Uuid $id = null
    ): Address {
        $address = new Address($city, $address, $house, $point, $postalCode, $street, $flat, $entrance, $floor, $settlement, $inputData);

        $reflectionClass = new \ReflectionClass(Address::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($address, $id ?: Uuid::v1());

        return $address;
    }

    public static function getOneFilled(
        ?City $city = null,
        ?string $address = null,
        ?string $house = null,
        ?Point $point = null,
        ?string $postalCode = null,
        ?string $street = null,
        ?string $flat = null,
        ?string $entrance = null,
        ?string $floor = null,
        ?string $settlement = null,
        ?array $inputData = [],
        ?Uuid $id = null
    ): Address {
        $address = new Address(
            $city ?: CityFixture::getOne(
                RegionFixture::getOne(
                    CountryFixture::getOne('Russia', 'RU'),
                    'Moscow',
                    'msk',
                ),
                'Moscow',
                'City',
            ),
            $address ?: '12345, Russia, Moscow, Lenin street 2/2',
            $house ?: '2/2',
            $point ?: PointValueFixture::getOne(41.23141, 42.231241),
            $postalCode ?: '12345',
            $street ?: 'Lenin street',
            $flat,
            $entrance,
            $floor,
            $settlement,
            $inputData
        );

        $reflectionClass = new \ReflectionClass(Address::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($address, $id ?: Uuid::v1());

        return $address;
    }

    public static function getOneForIsActive(
        City $city,
        string $address,
        string $house,
        ?Point $point,
        ?string $postalCode = null,
        ?string $street = null,
        ?string $flat = null,
        ?string $entrance = null,
        ?string $floor = null,
        ?string $settlement = null,
        ?array $inputData = [],
        bool $isActive = true,
        ?Uuid $id = null
    ): Address {
        $address = new Address($city, $address, $house, $point, $postalCode, $street, $flat, $entrance, $floor, $settlement, $inputData);

        $address->changeIsActive($isActive);

        $reflectionClass = new \ReflectionClass(Address::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($address, $id ?: Uuid::v1());

        return $address;
    }

    public static function getOneForDeleted(
        City $city,
        string $address,
        string $house,
        ?Point $point,
        ?string $postalCode = null,
        ?string $street = null,
        ?string $flat = null,
        ?string $entrance = null,
        ?string $floor = null,
        ?string $settlement = null,
        ?array $inputData = [],
        bool $deleted = true,
        ?Uuid $id = null
    ): Address {
        $address = new Address($city, $address, $house, $point, $postalCode, $street, $flat, $entrance, $floor, $settlement, $inputData);

        if ($deleted) {
            $address->deleted();
        }

        $reflectionClass = new \ReflectionClass(Address::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($address, $id ?: Uuid::v1());

        return $address;
    }

    public static function getOneFromAddressDto(City $city, AddressDto $dto, ?bool $isActive = null, bool $deleted = false, ?Uuid $id = null): Address
    {
        $address = new Address(
            $city,
            $dto->address,
            $dto->house,
            PointValueFixture::getOne($dto->latitude, $dto->longitude),
            $dto->postalCode,
            $dto->street,
            $dto->flat,
            $dto->entrance,
            $dto->floor,
            $dto->settlement,
            $dto->inputData
        );

        if (!is_null($isActive)) {
            $address->changeIsActive($isActive);
        }

        if ($deleted) {
            $address->deleted();
        }

        $reflectionClass = new \ReflectionClass(Address::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($address, $id ?: Uuid::v1());

        return $address;
    }
}
