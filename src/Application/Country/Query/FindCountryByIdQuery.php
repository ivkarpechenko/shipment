<?php

namespace App\Application\Country\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindCountryByIdQuery implements Query
{
    public function __construct(private Uuid $countryId)
    {
    }

    public function getCountryId(): Uuid
    {
        return $this->countryId;
    }
}
