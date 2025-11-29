<?php

namespace App\Application\Region\Command;

use App\Application\Command;

readonly class CreateRegionCommand implements Command
{
    public function __construct(private string $countryCode, private string $name, private string $code)
    {
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
