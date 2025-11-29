<?php

namespace App\Application\PickupPoint\Command;

use App\Application\Command;
use App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto\CdekPickupPointDto;

readonly class CreateCdekPickupPointCommand implements Command
{
    public function __construct(
        public CdekPickupPointDto $dto
    ) {
    }
}
