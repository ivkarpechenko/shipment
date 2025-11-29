<?php

declare(strict_types=1);

namespace App\Tests\Fixture\DeliveryMethod;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use Symfony\Component\Uid\Uuid;

final class DeliveryMethodFixture
{
    public static function getOne(string $code, string $name, ?Uuid $id = null): DeliveryMethod
    {
        $deliveryMethod = new DeliveryMethod($code, $name);

        $reflectionClass = new \ReflectionClass(DeliveryMethod::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($deliveryMethod, $id ?: Uuid::v1());

        return $deliveryMethod;
    }

    public static function getOneDeactivated(string $code, string $name, bool $isActive = true, ?Uuid $id = null): DeliveryMethod
    {
        $deliveryMethod = new DeliveryMethod($code, $name);
        $deliveryMethod->changeIsActive($isActive);

        $reflectionClass = new \ReflectionClass(DeliveryMethod::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($deliveryMethod, $id ?: Uuid::v1());

        return $deliveryMethod;
    }
}
