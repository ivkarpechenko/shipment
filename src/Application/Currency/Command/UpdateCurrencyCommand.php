<?php

namespace App\Application\Currency\Command;

use App\Application\Command;

readonly class UpdateCurrencyCommand implements Command
{
    public function __construct(private string $code, private ?string $name, private ?bool $isActive)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }
}
