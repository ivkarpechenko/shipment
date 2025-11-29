<?php

namespace App\Application\Currency\Query;

use App\Application\Query;

readonly class FindCurrencyByCodeQuery implements Query
{
    public function __construct(private string $code)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
