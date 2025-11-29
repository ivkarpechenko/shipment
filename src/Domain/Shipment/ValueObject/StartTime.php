<?php

namespace App\Domain\Shipment\ValueObject;

use App\Domain\Shipment\Exception\StoreScheduleInvalidTimeFormatException;

final readonly class StartTime
{
    private const FORMAT = 'H:i:s';

    private const LENGTH = 8;

    private string $value;

    public function __construct(string $value)
    {
        $this->validate($value);

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(StartTime $startTime): bool
    {
        return $this->value == $startTime->getValue();
    }

    private function validate(string $value): void
    {
        if (empty($value)) {
            return;
        }

        if (strlen($value) !== self::LENGTH) {
            throw new StoreScheduleInvalidTimeFormatException('Invalid start time format.');
        }

        $datetime = \DateTime::createFromFormat(self::FORMAT, $value);
        if (!$datetime && $datetime->format(self::FORMAT) == $value) {
            throw new StoreScheduleInvalidTimeFormatException('Store schedule invalid start time format');
        }
    }
}
