<?php

declare(strict_types=1);

namespace App\Tests\Fixture\PickupPoint;

use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinPickupPointDto;

final class DellinPickupPointDtoFixture
{
    public static function getOne(
        string $name = 'test',
        string $workTime = 'Пн-Пт 10:00-20:00, Сб 10:00-16:00, Вс 10:00-16:00',
        string $code = 'BEYe1',
        string $type = 'PickupPoint',
        ?float $weightMin = null,
        ?float $weightMax = null,
        string $address = '452009, Россия, Башкортостан, Белебей, ул. Революционеров, 1а',
        ?float $latitude = 55.737846,
        ?float $longitude = 37.72137,
        ?float $width = 10,
        ?float $height = 10,
        ?float $depth = 10,
        array $phones = [
            '+74957978108',
            '+79250424529',
        ]
    ): DellinPickupPointDto {
        return new DellinPickupPointDto(
            name: $name,
            code: $code,
            type: $type,
            weightMin: $weightMin,
            weightMax: $weightMax,
            address: $address,
            latitude: $latitude,
            longitude: $longitude,
            width: $width,
            height: $height,
            depth: $depth,
            phones: $phones,
            workTime: $workTime
        );
    }
}
