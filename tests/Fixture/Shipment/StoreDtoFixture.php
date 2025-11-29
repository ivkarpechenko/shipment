<?php

namespace App\Tests\Fixture\Shipment;

use App\Application\Shipment\Command\Dto\ContactDto;
use App\Application\Shipment\Command\Dto\StoreDto;
use App\Application\Shipment\Command\Dto\StoreScheduleDto;

class StoreDtoFixture
{
    public static function getOne(
        ContactDto $contact,
        int $externalId,
        int $maxWeight,
        int $maxVolume,
        int $maxLength,
        bool $isPickup,
        string $address,
        // @var StoreScheduleDto[] $schedules
        array $schedules,
        ?\DateTime $psd,
        ?\DateTime $psdStartTime,
        ?\DateTime $psdEndTime
    ): StoreDto {
        return new StoreDto($contact, $externalId, $maxWeight, $maxVolume, $maxLength, $isPickup, $address, $schedules, $psd, $psdStartTime, $psdEndTime);
    }

    public static function getOneFilled(
        ?ContactDto $contact = null,
        ?int $externalId = null,
        ?int $maxWeight = null,
        ?int $maxVolume = null,
        ?int $maxLength = null,
        ?bool $isPickup = null,
        ?string $address = null,
        // @var StoreScheduleDto[] $schedules
        array $schedules = [],
        ?\DateTime $psd = null,
        ?\DateTime $psdStartTime = null,
        ?\DateTime $psdEndTime = null
    ): StoreDto {
        return self::getOne(
            $contact ?? ContactDtoFixture::getOne('sender@gmail.com', 'sender', ['+777777777']),
            $externalId ?? 1,
            $maxWeight ?? 1,
            $maxVolume ?? 1,
            $maxLength ?? 1,
            $isPickup ?? false,
            $address ?? 'address',
            !empty($schedules) ? $schedules : [StoreScheduleDtoFixture::getOne(1, '10:00:00', '19:00:00')],
            $psd ?? new \DateTime(),
            $psdStartTime ?? new \DateTime(),
            $psdEndTime ?? new \DateTime(),
        );
    }
}
