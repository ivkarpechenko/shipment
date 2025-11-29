<?php

namespace App\Application\Shipment\Command;

use App\Application\Command;
use App\Application\Shipment\Command\Dto\CreateShipmentDto;

readonly class CreateShipmentCommand implements Command
{
    public function __construct(
        public CreateShipmentDto $createShipmentDto
    ) {
    }
}
