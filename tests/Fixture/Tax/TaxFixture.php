<?php

namespace App\Tests\Fixture\Tax;

use App\Domain\Country\Entity\Country;
use App\Domain\Tax\Entity\Tax;
use Symfony\Component\Uid\Uuid;

final class TaxFixture
{
    public static function getOne(Country $country, string $name, float $value, string $expression, ?Uuid $id = null): Tax
    {
        $tax = new Tax($country, $name, $value, $expression);

        $reflectionClass = new \ReflectionClass(Tax::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($tax, $id ?: Uuid::v1());

        return $tax;
    }

    public static function getOneForDeleted(Country $country, string $name, float $value, string $expression, bool $deleted = true, ?Uuid $id = null): Tax
    {
        $tax = new Tax($country, $name, $value, $expression);

        if ($deleted) {
            $tax->deleted();
        }

        $reflectionClass = new \ReflectionClass(Tax::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($tax, $id ?: Uuid::v1());

        return $tax;
    }
}
