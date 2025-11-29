<?php

namespace App\Application\Shipment\Query;

use App\Application\Query;

readonly class FindShipmentsByQuery implements Query
{
    public function __construct(private array $shipments)
    {
    }

    public function getShipments(): array
    {
        return $this->shipments;
    }
}
