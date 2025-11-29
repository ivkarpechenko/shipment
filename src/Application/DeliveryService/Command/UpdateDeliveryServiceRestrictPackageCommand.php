<?php

declare(strict_types=1);

namespace App\Application\DeliveryService\Command;

use App\Application\Command;
use Symfony\Component\Uid\Uuid;

readonly class UpdateDeliveryServiceRestrictPackageCommand implements Command
{
    public function __construct(
        public Uuid $id,
        public int $maxWeight,
        public int $maxWidth,
        public int $maxHeight,
        public int $maxLength,
        public bool $isActive
    ) {
    }
}
