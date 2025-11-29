<?php

namespace App\Application\Region\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindRegionByIdQuery implements Query
{
    public function __construct(private Uuid $regionId)
    {
    }

    public function getRegionId(): Uuid
    {
        return $this->regionId;
    }
}
