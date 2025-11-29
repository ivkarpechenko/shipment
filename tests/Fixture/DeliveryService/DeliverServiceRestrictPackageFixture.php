<?php

declare(strict_types=1);

namespace App\Tests\Fixture\DeliveryService;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictPackage;
use Symfony\Component\Uid\Uuid;

final class DeliverServiceRestrictPackageFixture
{
    public static function getOne(DeliveryService $deliveryService, int $maxWeight, int $maxWidth, int $maxHeight, int $maxLength, ?Uuid $id = null): DeliveryServiceRestrictPackage
    {
        $deliveryServiceRestrictPackage = new DeliveryServiceRestrictPackage(
            deliveryService: $deliveryService,
            maxWeight: $maxWeight,
            maxWidth: $maxWidth,
            maxHeight: $maxHeight,
            maxLength: $maxLength
        );

        $reflectionClass = new \ReflectionClass(DeliveryServiceRestrictPackage::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($deliveryServiceRestrictPackage, $id ?: Uuid::v1());

        return $deliveryServiceRestrictPackage;
    }
}
