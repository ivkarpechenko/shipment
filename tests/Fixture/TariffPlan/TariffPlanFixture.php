<?php

namespace App\Tests\Fixture\TariffPlan;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use Symfony\Component\Uid\Uuid;

final class TariffPlanFixture
{
    public static function getOne(DeliveryService $deliveryService, DeliveryMethod $deliveryMethod, string $code, string $name, ?Uuid $id = null): TariffPlan
    {
        $tariffPlan = new TariffPlan($deliveryService, $deliveryMethod, $code, $name);

        $reflectionClass = new \ReflectionClass(TariffPlan::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($tariffPlan, $id ?: Uuid::v1());

        return $tariffPlan;
    }

    public static function getOneDeactivated(DeliveryService $deliveryService, DeliveryMethod $deliveryMethod, string $code, string $name, bool $isActive = true, ?Uuid $id = null): TariffPlan
    {
        $tariffPlan = new TariffPlan($deliveryService, $deliveryMethod, $code, $name);

        $tariffPlan->changeIsActive($isActive);

        $reflectionClass = new \ReflectionClass(TariffPlan::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($tariffPlan, $id ?: Uuid::v1());

        return $tariffPlan;
    }

    public static function getOneFilled(
        ?DeliveryService $deliveryService = null,
        ?DeliveryMethod $deliveryMethod = null,
        ?string $code = null,
        ?string $name = null,
        ?Uuid $id = null
    ): TariffPlan {
        $tariffPlan = new TariffPlan(
            $deliveryService ?: DeliveryServiceFixture::getOne('cdek', 'test'),
            $deliveryMethod ?: DeliveryMethodFixture::getOne('courier', 'test'),
            $code ?: 'test',
            $name ?: 'test',
        );

        $reflectionClass = new \ReflectionClass(TariffPlan::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($tariffPlan, $id ?: Uuid::v1());

        return $tariffPlan;
    }
}
