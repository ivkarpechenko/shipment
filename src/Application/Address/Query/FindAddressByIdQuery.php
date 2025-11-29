<?php

namespace App\Application\Address\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindAddressByIdQuery implements Query
{
    public function __construct(private Uuid $addressId)
    {
    }

    public function getAddressId(): Uuid
    {
        return $this->addressId;
    }
}
