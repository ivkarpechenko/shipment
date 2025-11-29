<?php

declare(strict_types=1);

namespace App\Application\PickupPoint\Command;

use App\Application\Command;
use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinPickupPointDto;

readonly class CreateDellinPickupPointCommand implements Command
{
    public function __construct(
        public DellinPickupPointDto $dto,
    ) {
    }
}
