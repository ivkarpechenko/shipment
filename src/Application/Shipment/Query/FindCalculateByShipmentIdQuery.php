<?php

namespace App\Application\Shipment\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindCalculateByShipmentIdQuery implements Query
{
    public function __construct(private Uuid $shipmentId)
    {
    }

    public function getShipmentId(): Uuid
    {
        return $this->shipmentId;
    }
}
