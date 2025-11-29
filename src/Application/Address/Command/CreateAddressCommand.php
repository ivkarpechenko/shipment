<?php

namespace App\Application\Address\Command;

use App\Application\Command;

readonly class CreateAddressCommand implements Command
{
    public function __construct(private string $address)
    {
    }

    public function getAddress(): string
    {
        return $this->address;
    }
}
