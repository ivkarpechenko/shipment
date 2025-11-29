<?php

namespace App\Infrastructure\DBAL\Types\Doctrine\Store;

use App\Domain\Shipment\ValueObject\EndTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EndTimeType extends Type
{
    public const NAME = 'endTime';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'VARCHAR(10)';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): EndTime
    {
        return new EndTime($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof EndTime) {
            $value = $value->getValue();
        }

        return $value;
    }

    public function canRequireSQLConversion(): true
    {
        return true;
    }
}
