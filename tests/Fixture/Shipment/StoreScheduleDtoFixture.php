<?php

namespace App\Tests\Fixture\Shipment;

use App\Application\Shipment\Command\Dto\StoreScheduleDto;

class StoreScheduleDtoFixture
{
    public static function getOne(
        int $day,
        string $startTime,
        string $endTime
    ): StoreScheduleDto {
        return new StoreScheduleDto($day, $startTime, $endTime);
    }
}
