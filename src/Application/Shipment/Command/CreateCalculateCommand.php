<?php

namespace App\Application\Shipment\Command;

use App\Application\Command;
use Symfony\Component\Uid\Uuid;

readonly class CreateCalculateCommand implements Command
{
    public function __construct(
        public Uuid $shipmentId,
        public string $deliveryServiceCode,
        public string $deliveryMethodCode,
        public ?\DateTime $expiredAt = null
    ) {
    }
}
