<?php

namespace App\Tests\Fixture\PickupPoint;

use App\Domain\Address\ValueObject\Point;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\PickupPoint\Entity\PickupPoint;
use Symfony\Component\Uid\Uuid;

final class PickupPointFixture
{
    public static function getOne(
        DeliveryService $deliveryService,
        string $address = '452009, Россия, Башкортостан, Белебей, ул. Революционеров, 1а',
        string $code = 'BEYe1',
        string $type = 'PickupPoint',
        ?float $weightMin = null,
        ?float $weightMax = null,
        ?Uuid $id = null,
        string $workTime = 'Пн-Пт 10:00-20:00, Сб 10:00-16:00, Вс 10:00-16:00',
        ?float $latitude = 55.737846,
        ?float $longitude = 37.72137,
        ?float $width = 10,
        ?float $height = 10,
        ?float $depth = 10,
        array $phones = [
            '+74957978108',
            '+79250424529',
        ],
        string $name = 'test'
    ): PickupPoint {
        $pickupPoint = new PickupPoint(
            deliveryService: $deliveryService,
            phones: $phones,
            point: new Point($latitude, $longitude),
            address: $address,
            workTime: $workTime,
            name: $name,
            code: $code,
            type: $type,
            weightMin: $weightMin,
            weightMax: $weightMax,
            width: $width,
            height: $height,
            depth: $depth
        );

        $reflectionClass = new \ReflectionClass(PickupPoint::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($pickupPoint, $id ?: Uuid::v1());

        return $pickupPoint;
    }
}
