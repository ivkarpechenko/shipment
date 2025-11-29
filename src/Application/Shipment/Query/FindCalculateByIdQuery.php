<?php

namespace App\Application\Shipment\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindCalculateByIdQuery implements Query
{
    public function __construct(private Uuid $calculateId)
    {
    }

    public function getCalculateId(): Uuid
    {
        return $this->calculateId;
    }
}
