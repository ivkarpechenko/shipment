<?php

namespace App\Application\Tax\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindTaxByIdQuery implements Query
{
    public function __construct(private Uuid $taxId)
    {
    }

    public function getTaxId(): Uuid
    {
        return $this->taxId;
    }
}
