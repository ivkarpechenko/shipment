<?php

namespace App\Tests\Fixture\DeliveryService;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictArea;
use App\Domain\DeliveryService\ValueObject\Polygon;
use Symfony\Component\Uid\Uuid;

final class DeliveryServiceRestrictAreaFixture
{
    public static function getOne(
        DeliveryService $deliveryService,
        string $name,
        Polygon $polygon,
        ?Uuid $id = null
    ): DeliveryServiceRestrictArea {
        $deliveryServiceRestrictArea = new DeliveryServiceRestrictArea($deliveryService, $name, $polygon);

        $reflectionClass = new \ReflectionClass(DeliveryServiceRestrictArea::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($deliveryServiceRestrictArea, $id ?: Uuid::v1());

        return $deliveryServiceRestrictArea;
    }

    public static function getOneDeactivated(
        DeliveryService $deliveryService,
        string $name,
        Polygon $polygon,
        ?Uuid $id = null
    ): DeliveryServiceRestrictArea {
        $deliveryServiceRestrictArea = new DeliveryServiceRestrictArea($deliveryService, $name, $polygon);

        $deliveryServiceRestrictArea->changeIsActive(false);

        $reflectionClass = new \ReflectionClass(DeliveryServiceRestrictArea::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($deliveryServiceRestrictArea, $id ?: Uuid::v1());

        return $deliveryServiceRestrictArea;
    }
}
