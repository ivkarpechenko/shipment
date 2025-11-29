<?php

namespace App\Application\Shipment\Command;

use App\Application\Command;
use App\Application\Shipment\Command\Dto\UpdateShipmentDto;
use Symfony\Component\Uid\Uuid;

readonly class UpdateShipmentCommand implements Command
{
    public function __construct(
        public Uuid $shipmentId,
        public UpdateShipmentDto $updateShipmentDto
    ) {
    }
}
