<?php

namespace App\Infrastructure\DBAL\Types\Doctrine\Store;

use App\Domain\Shipment\ValueObject\StartTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class StartTimeType extends Type
{
    public const NAME = 'startTime';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'VARCHAR(10)';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): StartTime
    {
        return new StartTime($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof StartTime) {
            $value = $value->getValue();
        }

        return $value;
    }

    public function canRequireSQLConversion(): true
    {
        return true;
    }
}
