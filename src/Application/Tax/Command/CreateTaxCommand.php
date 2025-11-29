<?php

namespace App\Application\Tax\Command;

use App\Application\Command;

readonly class CreateTaxCommand implements Command
{
    public function __construct(
        private string $countryCode,
        private string $name,
        private float $value,
        private string $expression
    ) {
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
