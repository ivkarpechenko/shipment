<?php

namespace App\Application\Address\Query;

use App\Application\Query;
use App\Domain\City\Entity\City;

readonly class FindAddressesByCityQuery implements Query
{
    public function __construct(private City $city)
    {
    }

    public function getCity(): City
    {
        return $this->city;
    }
}
