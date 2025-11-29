<?php

namespace App\Tests\Fixture\Shipment;

use App\Domain\Shipment\Dto\CalculateDto;

class CalculateDtoFixture
{
    public static function getOne(
        int $minPeriod,
        int $maxPeriod,
        float $deliveryCost,
        float $deliveryTotalCost,
        float $deliveryTotalCostTax
    ): CalculateDto {
        return new CalculateDto($minPeriod, $maxPeriod, $deliveryCost, $deliveryTotalCost, $deliveryTotalCostTax);
    }
}
