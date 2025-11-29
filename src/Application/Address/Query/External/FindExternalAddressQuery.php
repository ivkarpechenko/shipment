<?php

namespace App\Application\Address\Query\External;

use App\Application\Query;

readonly class FindExternalAddressQuery implements Query
{
    public function __construct(private string $address)
    {
    }

    public function getAddress(): string
    {
        return $this->address;
    }
}
