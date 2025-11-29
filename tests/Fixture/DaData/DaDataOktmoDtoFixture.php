<?php

namespace App\Tests\Fixture\DaData;

use App\Infrastructure\DaData\Response\Dto\DaDataOktmoDto;

class DaDataOktmoDtoFixture
{
    public static function getOne(): DaDataOktmoDto
    {
        return new DaDataOktmoDto(
            oktmo: '45311000',
            areaType: '3',
            areaCode: '45311000',
            area: 'муниципальный округ Метрогородок',
            subareaType: null,
            subareaCode: null,
            subarea: null
        );
    }
}
