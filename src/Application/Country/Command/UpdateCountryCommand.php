<?php

namespace App\Application\Country\Command;

use App\Application\Command;
use Symfony\Component\Uid\Uuid;

readonly class UpdateCountryCommand implements Command
{
    public function __construct(private Uuid $countryId, private ?string $name, private ?bool $isActive)
    {
    }

    public function getCountryId(): Uuid
    {
        return $this->countryId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }
}
