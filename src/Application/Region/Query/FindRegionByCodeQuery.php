<?php

namespace App\Application\Region\Query;

use App\Application\Query;

readonly class FindRegionByCodeQuery implements Query
{
    public function __construct(private string $code)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
