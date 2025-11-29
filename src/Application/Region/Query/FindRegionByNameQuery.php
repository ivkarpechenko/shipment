<?php

namespace App\Application\Region\Query;

use App\Application\Query;

readonly class FindRegionByNameQuery implements Query
{
    public function __construct(private string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
