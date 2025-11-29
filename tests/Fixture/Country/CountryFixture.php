<?php

namespace App\Tests\Fixture\Country;

use App\Domain\Country\Entity\Country;
use Symfony\Component\Uid\Uuid;

final class CountryFixture
{
    public static function getOne(string $name, string $code, ?Uuid $id = null): Country
    {
        $country = new Country($name, $code);

        $reflectionClass = new \ReflectionClass(Country::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($country, $id ?: Uuid::v1());

        return $country;
    }

    public static function getOneForIsActive(string $name, string $code, bool $isActive = true, ?Uuid $id = null): Country
    {
        $country = new Country($name, $code);

        $country->changeIsActive($isActive);

        $reflectionClass = new \ReflectionClass(Country::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($country, $id ?: Uuid::v1());

        return $country;
    }

    public static function getOneForDeleted(string $name, string $code, bool $deleted = true, ?Uuid $id = null): Country
    {
        $country = new Country($name, $code);

        if ($deleted) {
            $country->deleted();
        }

        $reflectionClass = new \ReflectionClass(Country::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($country, $id ?: Uuid::v1());

        return $country;
    }
}
