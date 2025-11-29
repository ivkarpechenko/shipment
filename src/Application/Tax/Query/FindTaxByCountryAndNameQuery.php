<?php

namespace App\Application\Tax\Query;

use App\Application\Query;
use App\Domain\Country\Entity\Country;

readonly class FindTaxByCountryAndNameQuery implements Query
{
    public function __construct(private Country $country, private string $name)
    {
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
