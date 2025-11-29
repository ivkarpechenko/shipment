<?php

namespace App\Application\DeliveryService\Command;

use App\Application\CommandHandler;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictArea;
use App\Domain\DeliveryService\Service\CreateDeliveryServiceRestrictAreaService;

readonly class CreateDeliveryServiceRestrictAreaCommandHandler implements CommandHandler
{
    public function __construct(public CreateDeliveryServiceRestrictAreaService $createDeliveryServiceRestrictAreaService)
    {
    }

    public function __invoke(CreateDeliveryServiceRestrictAreaCommand $command): DeliveryServiceRestrictArea
    {
        return $this->createDeliveryServiceRestrictAreaService->create(
            $command->getDeliveryServiceId(),
            $command->getName(),
            $command->getPolygon()
        );
    }
}
