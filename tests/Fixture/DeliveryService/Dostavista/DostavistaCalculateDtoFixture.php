<?php

namespace App\Tests\Fixture\DeliveryService\Dostavista;

use App\Infrastructure\DeliveryService\Dostavista\Service\Response\Dto\DostavistaCalculateDto;

class DostavistaCalculateDtoFixture
{
    public static function getOne(
        int $minPeriod,
        int $maxPeriod,
        string $paymentAmount,
        string $deliveryFeeAmount,
        string $weightFeeAmount,
        string $insuranceAmount,
        string $insuranceFeeAmount,
        string $loadingFeeAmount,
        string $moneyTransferFeeAmount,
        string $overnightFeeAmount,
        string $doorToDoorFeeAmount,
        string $promoCodeDiscountAmount,
        string $backPaymentAmount,
        string $codFeeAmount,
    ): DostavistaCalculateDto {
        return new DostavistaCalculateDto(
            $minPeriod,
            $maxPeriod,
            $paymentAmount,
            $deliveryFeeAmount,
            $weightFeeAmount,
            $insuranceAmount,
            $insuranceFeeAmount,
            $loadingFeeAmount,
            $moneyTransferFeeAmount,
            $overnightFeeAmount,
            $doorToDoorFeeAmount,
            $promoCodeDiscountAmount,
            $backPaymentAmount,
            $codFeeAmount
        );
    }

    public static function getOneFilled(
        ?int $minPeriod = null,
        ?int $maxPeriod = null,
        ?string $paymentAmount = null,
        ?string $deliveryFeeAmount = null,
    ): DostavistaCalculateDto {
        return new DostavistaCalculateDto(
            $minPeriod ?: 1,
            $maxPeriod ?: 10,
            $paymentAmount ?: 100,
            $deliveryFeeAmount ?: 100,
            100,
            100,
            100,
            100,
            100,
            100,
            100,
            100,
            100,
            100
        );
    }
}
