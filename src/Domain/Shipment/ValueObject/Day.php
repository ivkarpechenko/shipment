<?php

namespace App\Domain\Shipment\ValueObject;

use App\Domain\Shipment\Exception\StoreScheduleInvalidDayFormatException;

final readonly class Day
{
    private const MIN = 1;

    private const MAX = 7;

    private const LENGTH = 1;

    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(Day $day): bool
    {
        return $this->value == $day->getValue();
    }

    private function validate(int $value): void
    {
        if (strlen($value) !== self::LENGTH) {
            throw new StoreScheduleInvalidDayFormatException('Store schedule invalid day format.');
        }

        if (!filter_var(
            $value,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => self::MIN,
                    'max_range' => self::MAX,
                ],
            ]
        )) {
            throw new StoreScheduleInvalidDayFormatException('Store schedule invalid day format.');
        }
    }
}
