<?php

namespace App\Domain\Shipment\Dto;

class CalculateDto
{
    public function __construct(
        public int $minPeriod,
        public int $maxPeriod,
        public float $deliveryCost,
        public float $deliveryTotalCost,
        public float $deliveryTotalCostTax
    ) {
    }
}
