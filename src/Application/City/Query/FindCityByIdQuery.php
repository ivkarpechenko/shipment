<?php

namespace App\Application\City\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindCityByIdQuery implements Query
{
    public function __construct(private Uuid $cityId)
    {
    }

    public function getCityId(): Uuid
    {
        return $this->cityId;
    }
}
