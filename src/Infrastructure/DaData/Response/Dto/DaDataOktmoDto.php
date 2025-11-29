<?php

namespace App\Infrastructure\DaData\Response\Dto;

readonly class DaDataOktmoDto
{
    public function __construct(
        public string $oktmo,
        public ?string $areaType,
        public ?string $areaCode,
        public ?string $area,
        public ?string $subareaType,
        public ?string $subareaCode,
        public ?string $subarea,
    ) {
    }
}
