<?php

namespace App\Application\Currency\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindCurrencyByIdQuery implements Query
{
    public function __construct(private Uuid $currencyId)
    {
    }

    public function getCurrencyId(): Uuid
    {
        return $this->currencyId;
    }
}
