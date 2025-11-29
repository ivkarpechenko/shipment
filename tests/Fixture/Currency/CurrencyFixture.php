<?php

namespace App\Tests\Fixture\Currency;

use App\Domain\Currency\Entity\Currency;
use Symfony\Component\Uid\Uuid;

final class CurrencyFixture
{
    public static function getOne(string $code, int $num, string $name, ?Uuid $id = null): Currency
    {
        $currency = new Currency($code, $num, $name);

        $reflectionClass = new \ReflectionClass(Currency::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($currency, $id ?: Uuid::v1());

        return $currency;
    }

    public static function getOneDeactivated(string $code, int $num, string $name, ?Uuid $id = null, bool $isActive = true): Currency
    {
        $currency = new Currency($code, $num, $name);
        $currency->changeIsActive($isActive);

        $reflectionClass = new \ReflectionClass(Currency::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($currency, $id ?: Uuid::v1());

        return $currency;
    }
}
