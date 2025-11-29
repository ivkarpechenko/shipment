<?php

namespace App\Tests\Fixture\Region;

use App\Domain\Country\Entity\Country;
use App\Domain\Region\Entity\Region;
use Symfony\Component\Uid\Uuid;

final class RegionFixture
{
    public static function getOne(Country $country, string $name, string $code, ?Uuid $id = null): Region
    {
        $region = new Region($country, $name, $code);

        $reflectionClass = new \ReflectionClass(Region::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($region, $id ?: Uuid::v1());

        return $region;
    }

    public static function getOneForIsActive(Country $country, string $name, string $code, bool $isActive = true, ?Uuid $id = null): Region
    {
        $region = new Region($country, $name, $code);

        $region->changeIsActive($isActive);

        $reflectionClass = new \ReflectionClass(Region::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($region, $id ?: Uuid::v1());

        return $region;
    }

    public static function getOneForDeleted(Country $country, string $name, string $code, bool $deleted = true, ?Uuid $id = null): Region
    {
        $region = new Region($country, $name, $code);

        if ($deleted) {
            $region->deleted();
        }

        $reflectionClass = new \ReflectionClass(Region::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($region, $id ?: Uuid::v1());

        return $region;
    }
}
