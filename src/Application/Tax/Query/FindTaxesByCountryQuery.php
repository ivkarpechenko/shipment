<?php

namespace App\Application\Tax\Query;

use App\Application\Query;
use App\Domain\Country\Entity\Country;

readonly class FindTaxesByCountryQuery implements Query
{
    public function __construct(private Country $country)
    {
    }

    public function getCountry(): Country
    {
        return $this->country;
    }
}
