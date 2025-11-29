<?php

namespace App\Application\City\Query;

use App\Application\Query;

readonly class FindCityByTypeAndNameQuery implements Query
{
    public function __construct(private string $type, private string $name)
    {
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
