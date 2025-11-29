<?php

declare(strict_types=1);

namespace App\Domain\Directory\Dto;

readonly class OkatoOktmoDto
{
    public function __construct(
        public string $okato,
        public string $oktmo,
        public ?string $location = null
    ) {
    }
}
