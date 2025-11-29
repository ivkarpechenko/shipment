<?php

namespace App\Tests\Fixture\Shipment;

use App\Domain\Shipment\Entity\StoreSchedule;
use App\Domain\Shipment\ValueObject\Day;
use App\Domain\Shipment\ValueObject\EndTime;
use App\Domain\Shipment\ValueObject\StartTime;
use Symfony\Component\Uid\Uuid;

class StoreScheduleFixture
{
    public static function getOne(
        Day $day,
        StartTime $startTime,
        EndTime $endTime
    ): StoreSchedule {
        $storeSchedule = new StoreSchedule($day, $startTime, $endTime);

        $reflectionClass = new \ReflectionClass(StoreSchedule::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($storeSchedule, Uuid::v1());

        return $storeSchedule;
    }

    public static function getOneFilled(
        int $day,
        string $startTime,
        string $endTime
    ): StoreSchedule {
        return self::getOne(new Day($day), new StartTime($startTime), new EndTime($endTime));
    }
}
