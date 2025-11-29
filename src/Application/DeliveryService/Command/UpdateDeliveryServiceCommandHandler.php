<?php

namespace App\Application\DeliveryService\Command;

use App\Application\CommandHandler;
use App\Domain\DeliveryService\Service\UpdateDeliveryServiceService;

readonly class UpdateDeliveryServiceCommandHandler implements CommandHandler
{
    public function __construct(public UpdateDeliveryServiceService $updateDeliveryServiceService)
    {
    }

    public function __invoke(UpdateDeliveryServiceCommand $command): void
    {
        $this->updateDeliveryServiceService->update($command->getCode(), $command->getName(), $command->getIsActive());
    }
}
