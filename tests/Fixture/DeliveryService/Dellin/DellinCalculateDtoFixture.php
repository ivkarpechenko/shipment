<?php

namespace App\Tests\Fixture\DeliveryService\Dellin;

use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinCalculateDto;

class DellinCalculateDtoFixture
{
    public static function getOne(
        float $derivalPrice,
        float $arrivalPrice,
        int $deliveryTerm,
        float $insurance,
        int $minPeriod,
        int $maxPeriod,
        float $deliverySum,
        float $totalSum
    ): DellinCalculateDto {
        return new DellinCalculateDto(
            $derivalPrice,
            $arrivalPrice,
            $deliveryTerm,
            $insurance,
            $minPeriod,
            $maxPeriod,
            $deliverySum,
            $totalSum
        );
    }

    public static function getOneFilled(
        ?float $derivalPrice = null,
        ?float $arrivalPrice = null,
        ?int $deliveryTerm = null,
        ?float $insurance = null,
        ?int $minPeriod = null,
        ?int $maxPeriod = null,
        ?float $deliverySum = null,
        ?float $totalSum = null
    ): DellinCalculateDto {
        return new DellinCalculateDto(
            $derivalPrice ?: 3030.0,
            $arrivalPrice ?: 2510.0,
            $deliveryTerm ?: 0,
            $insurance ?: 740.0,
            $minPeriod ?: 5,
            $maxPeriod ?: 7,
            $deliverySum ?: 10043.0,
            $totalSum ?: 10043.0
        );
    }
}
