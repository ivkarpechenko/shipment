<?php

namespace App\Tests\Fixture\City;

use App\Domain\City\Entity\City;
use App\Domain\Region\Entity\Region;
use Symfony\Component\Uid\Uuid;

final class CityFixture
{
    public static function getOne(Region $region, string $type, string $name, ?Uuid $id = null): City
    {
        $city = new City($region, $type, $name);

        $reflectionClass = new \ReflectionClass(City::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($city, $id ?: Uuid::v1());

        return $city;
    }

    public static function getOneForIsActive(Region $region, string $type, string $name, bool $isActive = true, ?Uuid $id = null): City
    {
        $city = new City($region, $type, $name);

        $city->changeIsActive($isActive);

        $reflectionClass = new \ReflectionClass(City::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($city, $id ?: Uuid::v1());

        return $city;
    }

    public static function getOneForDeleted(Region $region, string $type, string $name, bool $deleted = true, ?Uuid $id = null): City
    {
        $city = new City($region, $type, $name);

        if ($deleted) {
            $city->deleted();
        }

        $reflectionClass = new \ReflectionClass(City::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($city, $id ?: Uuid::v1());

        return $city;
    }
}
