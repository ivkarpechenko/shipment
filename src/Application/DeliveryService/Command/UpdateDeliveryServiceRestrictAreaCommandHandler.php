<?php

namespace App\Application\DeliveryService\Command;

use App\Application\CommandHandler;
use App\Domain\DeliveryService\Service\UpdateDeliveryServiceRestrictAreaService;

readonly class UpdateDeliveryServiceRestrictAreaCommandHandler implements CommandHandler
{
    public function __construct(public UpdateDeliveryServiceRestrictAreaService $updateDeliveryServiceRestrictAreaService)
    {
    }

    public function __invoke(UpdateDeliveryServiceRestrictAreaCommand $command): void
    {
        $this->updateDeliveryServiceRestrictAreaService->update(
            $command->getDeliveryServiceRestrictAreaId(),
            $command->getName(),
            $command->getPolygon(),
            $command->getIsActive()
        );
    }
}
