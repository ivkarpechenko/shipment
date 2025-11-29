<?php

namespace App\Application\Tax\Command;

use App\Application\Command;
use Symfony\Component\Uid\Uuid;

readonly class UpdateTaxCommand implements Command
{
    public function __construct(private Uuid $taxId, private float $value)
    {
    }

    public function getTaxId(): Uuid
    {
        return $this->taxId;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
