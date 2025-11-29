<?php

namespace App\Tests\Fixture\DeliveryService;

use App\Domain\DeliveryService\Entity\DeliveryService;
use Symfony\Component\Uid\Uuid;

final class DeliveryServiceFixture
{
    public static function getOne(string $code, string $name, ?Uuid $id = null): DeliveryService
    {
        $deliveryService = new DeliveryService($code, $name);

        $reflectionClass = new \ReflectionClass(DeliveryService::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($deliveryService, $id ?: Uuid::v1());

        return $deliveryService;
    }

    public static function getOneDeactivated(string $code, string $name, bool $isActive = true, ?Uuid $id = null): DeliveryService
    {
        $deliveryService = new DeliveryService($code, $name);

        $deliveryService->changeIsActive($isActive);

        $reflectionClass = new \ReflectionClass(DeliveryService::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($deliveryService, $id ?: Uuid::v1());

        return $deliveryService;
    }
}
