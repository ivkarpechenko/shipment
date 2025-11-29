<?php

namespace App\Application\City\Command;

use App\Application\Command;

readonly class CreateCityCommand implements Command
{
    public function __construct(private string $regionCode, private string $type, private string $name)
    {
    }

    public function getRegionCode(): string
    {
        return $this->regionCode;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
