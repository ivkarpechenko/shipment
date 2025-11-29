<?php

namespace App\Infrastructure\DBAL\Types\Doctrine\Store;

use App\Domain\Shipment\ValueObject\Day;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class DayType extends Type
{
    public const NAME = 'day';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'SMALLINT';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): Day
    {
        return new Day($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Day) {
            $value = $value->getValue();
        }

        return $value;
    }

    public function canRequireSQLConversion(): true
    {
        return true;
    }
}
