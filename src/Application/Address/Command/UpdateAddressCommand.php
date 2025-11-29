<?php

namespace App\Application\Address\Command;

use App\Application\Command;
use Symfony\Component\Uid\Uuid;

readonly class UpdateAddressCommand implements Command
{
    public function __construct(private Uuid $AddressId, private ?bool $isActive)
    {
    }

    public function getAddressId(): Uuid
    {
        return $this->AddressId;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }
}
