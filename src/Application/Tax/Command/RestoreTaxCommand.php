<?php

namespace App\Application\Tax\Command;

use App\Application\Command;
use Symfony\Component\Uid\Uuid;

readonly class RestoreTaxCommand implements Command
{
    public function __construct(private Uuid $taxId)
    {
    }

    public function getTaxId(): Uuid
    {
        return $this->taxId;
    }
}
