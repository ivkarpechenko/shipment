<?php

namespace App\Application\Shipment\Command;

use App\Application\Command;
use Symfony\Component\Uid\Uuid;

readonly class ExpireCalculateCommand implements Command
{
    public function __construct(private Uuid $shipmentId)
    {
    }

    public function getShipmentId(): Uuid
    {
        return $this->shipmentId;
    }
}
