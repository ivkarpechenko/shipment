<?php

namespace App\Application\DeliveryService\Command;

use App\Application\CommandHandler;
use App\Domain\DeliveryService\Service\CreateDeliveryServiceService;

readonly class CreateDeliveryServiceCommandHandler implements CommandHandler
{
    public function __construct(public CreateDeliveryServiceService $createDeliveryServiceService)
    {
    }

    public function __invoke(CreateDeliveryServiceCommand $command): void
    {
        $this->createDeliveryServiceService->create($command->getCode(), $command->getName());
    }
}
