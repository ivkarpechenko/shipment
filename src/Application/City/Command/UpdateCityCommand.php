<?php

namespace App\Application\City\Command;

use App\Application\Command;
use Symfony\Component\Uid\Uuid;

readonly class UpdateCityCommand implements Command
{
    public function __construct(private Uuid $cityId, private ?string $type, private ?string $name, private ?bool $isActive)
    {
    }

    public function getCityId(): Uuid
    {
        return $this->cityId;
    }

    public function getType(): ?string
    {
        return $this->type;
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
