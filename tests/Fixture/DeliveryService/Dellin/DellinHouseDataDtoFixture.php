<?php

namespace App\Tests\Fixture\DeliveryService\Dellin;

use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinHouseDataDto;

class DellinHouseDataDtoFixture
{
    public static function getOne(
        ?string $houseNumber,
        ?string $fraction,
        ?string $letter,
        ?string $building,
        ?string $structure
    ): DellinHouseDataDto {
        return new DellinHouseDataDto(
            $houseNumber,
            $fraction,
            $letter,
            $building,
            $structure
        );
    }

    public static function getOneFilled(
        ?string $houseNumber = null,
        ?string $fraction = null,
        ?string $letter = null,
        ?string $building = null,
        ?string $structure = null
    ): DellinHouseDataDto {
        return new DellinHouseDataDto(
            $houseNumber ?: '5',
            $fraction ?: '1',
            $letter ?: '1',
            $building ?: '1',
            $structure ?: '1'
        );
    }
}
