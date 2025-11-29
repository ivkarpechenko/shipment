<?php

namespace App\Infrastructure\DBAL\Types\Doctrine;

use App\Domain\DeliveryService\ValueObject\Point;
use App\Domain\DeliveryService\ValueObject\Polygon;
use CrEOF\Geo\WKT\Parser;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class PolygonType extends Type
{
    public const TYPE = 'polygon';

    public function getName(): string
    {
        return self::TYPE;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'GEOMETRY';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): Polygon
    {
        $coordinates = (new Parser($value))->parse();

        $coordinates = array_map(function (array $coordinates) {
            return array_map(function (array $coordinate) {
                return new Point($coordinate[1], $coordinate[0]);
            }, $coordinates);
        }, $coordinates['value']);

        return new Polygon($coordinates);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Polygon) {
            $coordinates = array_map(function (array $lines) {
                $lineString = implode(',', array_map(function (Point $point) {
                    return sprintf('%F %F', $point->getLongitude(), $point->getLatitude());
                }, $lines));

                return '(' . $lineString . ')';
            }, $value->getCoordinates());

            return strtoupper(self::TYPE) . '(' . implode(',', $coordinates) . ')';
        }

        return $value;
    }

    public function canRequireSQLConversion(): bool
    {
        return true;
    }

    public function convertToPHPValueSQL($sqlExpr, $platform): string
    {
        return 'ST_AsText(' . $sqlExpr . ')';
    }
}
