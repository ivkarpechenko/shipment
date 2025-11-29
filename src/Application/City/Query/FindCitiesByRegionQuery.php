<?php

namespace App\Application\City\Query;

use App\Application\Query;
use App\Domain\Region\Entity\Region;

readonly class FindCitiesByRegionQuery implements Query
{
    public function __construct(private Region $region)
    {
    }

    public function getRegion(): Region
    {
        return $this->region;
    }
}
