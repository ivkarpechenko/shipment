<?php

namespace App\Application\Region\Query;

use App\Application\Query;
use App\Domain\Country\Entity\Country;

readonly class FindRegionsByCountryQuery implements Query
{
    public function __construct(private Country $country)
    {
    }

    public function getCountry(): Country
    {
        return $this->country;
    }
}
