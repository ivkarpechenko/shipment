<?php

namespace App\Application\Address\Command;

use App\Application\Command;
use Symfony\Component\Uid\Uuid;

readonly class DeleteAddressCommand implements Command
{
    public function __construct(private Uuid $addressId)
    {
    }

    public function getAddressId(): Uuid
    {
        return $this->addressId;
    }
}
