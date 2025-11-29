<?php

declare(strict_types=1);

namespace App\Tests\Fixture\Shipment;

use App\Application\Shipment\Command\Dto\CargoRestrictionDto;

class CargoRestrictionDtoFixture
{
    public static function getOne(
        string $code,
        int $maxWidth,
        int $maxHeight,
        int $maxLength,
        int $maxWeight,
        int $maxVolume,
        int $maxSumDimensions,
    ): CargoRestrictionDto {
        return new CargoRestrictionDto($code, $maxWidth, $maxHeight, $maxLength, $maxWeight, $maxVolume, $maxSumDimensions);
    }

    public static function getOneFilled(
        ?string $code = null,
        ?int $maxWidth = null,
        ?int $maxHeight = null,
        ?int $maxLength = null,
        ?int $maxWeight = null,
        ?int $maxVolume = null,
        ?int $maxSumDimensions = null,
    ): CargoRestrictionDto {
        return new CargoRestrictionDto(
            $code ?: 'test',
            $maxWidth ?: 100,
            $maxHeight ?: 200,
            $maxLength ?: 300,
            $maxWeight ?: 400,
            $maxVolume ?: 500,
            $maxSumDimensions ?: 600,
        );
    }
}
