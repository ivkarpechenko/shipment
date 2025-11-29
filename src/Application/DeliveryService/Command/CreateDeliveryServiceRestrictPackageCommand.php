<?php

declare(strict_types=1);

namespace App\Application\DeliveryService\Command;

use App\Application\Command;
use Symfony\Component\Uid\Uuid;

readonly class CreateDeliveryServiceRestrictPackageCommand implements Command
{
    public function __construct(
        public Uuid $deliveryServiceId,
        public int $maxWeight,
        public int $maxWidth,
        public int $maxHeight,
        public int $maxLength
    ) {
    }
}
