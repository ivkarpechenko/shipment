<?php

namespace App\Application\Address\Query\External;

use App\Domain\Address\Service\Dto\AddressDto;

interface FindExternalAddressInterface
{
    public function find(string $address): ?AddressDto;
}
