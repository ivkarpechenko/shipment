<?php

namespace App\Application\Shipment\Command;

use App\Application\CommandHandler;
use App\Domain\Shipment\Service\ExpireCalculateService;

readonly class ExpireCalculateCommandHandler implements CommandHandler
{
    public function __construct(public ExpireCalculateService $service)
    {
    }

    public function __invoke(ExpireCalculateCommand $command): void
    {
        $this->service->expire($command->getShipmentId());
    }
}
