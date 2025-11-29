<?php

namespace App\Application\DeliveryService\Query;

use App\Application\Query;

readonly class FindDeliveryServiceByCodeDeactivatedQuery implements Query
{
    public function __construct(private string $code)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
