<?php

namespace App\Application\Region\Command;

use App\Application\Command;
use Symfony\Component\Uid\Uuid;

readonly class UpdateRegionCommand implements Command
{
    public function __construct(private Uuid $regionId, private ?string $name, private ?bool $isActive)
    {
    }

    public function getRegionId(): Uuid
    {
        return $this->regionId;
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
