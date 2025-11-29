<?php

namespace App\Infrastructure\DBAL\Types\Doctrine;

use App\Domain\Address\ValueObject\Point;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class PointType extends Type
{
    public const POINT = 'point';

    public function getName(): string
    {
        return self::POINT;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'POINT';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Point
    {
        list($longitude, $latitude) = sscanf($value, '(%f,%f)');

        $point = null;
        if (!is_null($longitude) && !is_null($latitude)) {
            $point = new Point($latitude, $longitude);
        }

        return $point;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Point) {
            $value = sprintf('(%F,%F)', $value->getLongitude(), $value->getLatitude());
        }

        return $value;
    }

    public function canRequireSQLConversion(): bool
    {
        return true;
    }
}
