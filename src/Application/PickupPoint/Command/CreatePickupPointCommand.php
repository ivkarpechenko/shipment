<?php

namespace App\Application\PickupPoint\Command;

use App\Application\Command;
use App\Domain\PickupPoint\Service\Dto\PickupPointDto;

readonly class CreatePickupPointCommand implements Command
{
    public function __construct(
        public PickupPointDto $dto
    ) {
    }
}
