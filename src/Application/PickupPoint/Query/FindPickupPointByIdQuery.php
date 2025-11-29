<?php

namespace App\Application\PickupPoint\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindPickupPointByIdQuery implements Query
{
    public function __construct(public Uuid $pickupPointId)
    {
    }
}
