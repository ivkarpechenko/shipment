<?php

namespace App\Application\Country\Query;

use App\Application\Query;

readonly class FindCountryByNameQuery implements Query
{
    public function __construct(private string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
