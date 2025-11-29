<?php

namespace App\Application\Country\Command;

use App\Application\Command;

readonly class CreateCountryCommand implements Command
{
    public function __construct(private string $name, private string $code)
    {
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
