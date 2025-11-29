<?php

namespace App\Application\Shipment\Command;

use App\Application\Command;
use App\Application\Shipment\Command\Dto\BulkCreateShipmentTekTorgDto;

readonly class TekTorgCreateShipmentCommand implements Command
{
    public function __construct(private BulkCreateShipmentTekTorgDto $bulkCreateShipmentDto)
    {
    }

    public function getBulkCreateShipmentDto(): BulkCreateShipmentTekTorgDto
    {
        return $this->bulkCreateShipmentDto;
    }
}
