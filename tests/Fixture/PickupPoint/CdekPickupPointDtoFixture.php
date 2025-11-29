<?php

namespace App\Tests\Fixture\PickupPoint;

use App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto\CdekPickupPointDto;

final class CdekPickupPointDtoFixture
{
    public static function getOne(
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
    ): CdekPickupPointDto {
        return new CdekPickupPointDto(
            workTime: $workTime,
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
            phones: $phones
        );
    }
}
