<?php

namespace App\Application\Shipment\Command;

use App\Application\Command;
use Symfony\Component\Uid\Uuid;

readonly class TekTorgCreateCalculateCommand implements Command
{
    public function __construct(
        public Uuid $shipmentId,
        public ?\DateTime $expiredAt = null
    ) {
    }
}
