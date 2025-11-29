<?php

namespace App\Application\Country\Query;

use App\Application\Query;

readonly class FindCountryByCodeQuery implements Query
{
    public function __construct(private string $code)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
