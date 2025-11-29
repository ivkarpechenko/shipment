<?php

namespace App\Application\Shipment\Command\Dto;

final class StoreDto
{
    public function __construct(
        public ContactDto $contact,
        public int $externalId,
        public int $maxWeight,
        public int $maxVolume,
        public int $maxLength,
        public bool $isPickup,
        public string $address,
        /** @var StoreScheduleDto[] $schedules */
        public array $schedules,
        public ?\DateTime $psd,
        public ?\DateTime $psdStartTime,
        public ?\DateTime $psdEndTime
    ) {
    }

    public static function fromArray(array $store): self
    {
        return new self(
            ContactDto::fromArray($store['contact']),
            (int) $store['externalId'],
            (int) $store['maxWeight'],
            (int) $store['maxVolume'],
            (int) $store['maxLength'],
            (bool) $store['isPickup'],
            (string) $store['address'],
            array_map(function ($schedule) {
                return StoreScheduleDto::fromArray($schedule);
            }, $store['schedules']),
            $store['psd']
                ? \DateTime::createFromFormat('Y-m-d', $store['psd'])
                : null,
            $store['psdStartTime']
                ? \DateTime::createFromFormat('H:i:s', $store['psdStartTime'])
                : null,
            $store['psdEndTime']
                ? \DateTime::createFromFormat('H:i:s', $store['psdEndTime'])
                : null,
        );
    }
}
