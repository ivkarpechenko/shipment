<?php

namespace App\Tests\Fixture\Shipment;

use App\Domain\Shipment\Entity\Calculate;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use Symfony\Component\Uid\Uuid;

class CalculateFixture
{
    public static function getOne(
        Shipment $shipment,
        TariffPlan $tariffPlan,
        int $minPeriod,
        int $maxPeriod,
        float $deliveryCost,
        float $deliveryTotalCost,
        float $deliveryTotalCostVat
    ): Calculate {
        $calculate = new Calculate(
            $shipment,
            $tariffPlan,
            $minPeriod,
            $maxPeriod,
            $deliveryCost,
            $deliveryTotalCost,
            $deliveryTotalCostVat
        );

        $reflectionClass = new \ReflectionClass(Calculate::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($calculate, Uuid::v1());

        return $calculate;
    }

    public static function getOneFilled(
        ?Shipment $shipment = null,
        ?TariffPlan $tariffPlan = null,
        ?int $minPeriod = null,
        ?int $maxPeriod = null,
        ?float $deliveryCost = null,
        ?float $deliveryTotalCost = null,
        ?float $deliveryTotalCostVat = null
    ): Calculate {
        $calculate = new Calculate(
            $shipment ?: ShipmentFixture::getOneFilled(),
            $tariffPlan ?: TariffPlanFixture::getOneFilled(),
            $minPeriod ?: 4,
            $maxPeriod ?: 4,
            $deliveryCost ?: 1.1,
            $deliveryTotalCost ?: 2.2,
            $deliveryTotalCostVat ?: 0.1,
        );

        $reflectionClass = new \ReflectionClass(Calculate::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($calculate, Uuid::v1());

        return $calculate;
    }
}
