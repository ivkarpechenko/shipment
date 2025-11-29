<?php

namespace App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto;

class CdekCalculateDto
{
    public function __construct(
        public int $periodMin,
        public string $currency,
        public float $deliverySum,
        public int $weightCalc,
        public int $periodMax,
        public float $totalSum
    ) {
    }
}
