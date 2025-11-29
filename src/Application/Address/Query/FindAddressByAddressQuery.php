<?php

namespace App\Application\Address\Query;

use App\Application\Query;

readonly class FindAddressByAddressQuery implements Query
{
    public function __construct(private string $address)
    {
    }

    public function getAddress(): string
    {
        return $this->address;
    }
}
