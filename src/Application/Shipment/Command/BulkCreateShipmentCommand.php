<?php

namespace App\Application\Shipment\Command;

use App\Application\Command;
use App\Application\Shipment\Command\Dto\BulkCreateShipmentDto;

readonly class BulkCreateShipmentCommand implements Command
{
    public function __construct(private BulkCreateShipmentDto $bulkCreateShipmentDto)
    {
    }

    public function getBulkCreateShipmentDto(): BulkCreateShipmentDto
    {
        return $this->bulkCreateShipmentDto;
    }
}
