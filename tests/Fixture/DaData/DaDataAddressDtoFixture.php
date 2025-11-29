<?php

namespace App\Tests\Fixture\DaData;

use App\Domain\Address\Service\Dto\AddressDto;

class DaDataAddressDtoFixture
{
    public static function getOne(): AddressDto
    {
        return new AddressDto(
            '309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1',
            '1/1',
            'Россия',
            'RU',
            'Белгородская',
            'RU-BEL',
            'Алексеевка',
            'город',
            41.23141,
            42.231241,
            '309850',
            'ул Слободская',
            '',
            null,
            null,
            null,
            []
        );
    }
}
