<?php

namespace App\Application\Shipment\Command\Dto;

final class StoreScheduleDto
{
    public function __construct(
        public int $day,
        public string $startTime,
        public string $endTime
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['day'],
            $data['startTime'],
            $data['endTime']
        );
    }
}
