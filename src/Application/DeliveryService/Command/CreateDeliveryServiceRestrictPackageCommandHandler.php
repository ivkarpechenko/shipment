<?php

declare(strict_types=1);

namespace App\Application\DeliveryService\Command;

use App\Application\CommandHandler;
use App\Domain\DeliveryService\Service\CreateDeliveryServiceRestrictPackageService;

readonly class CreateDeliveryServiceRestrictPackageCommandHandler implements CommandHandler
{
    public function __construct(
        public CreateDeliveryServiceRestrictPackageService $createDeliveryServiceRestrictPackageService
    ) {
    }

    public function __invoke(CreateDeliveryServiceRestrictPackageCommand $command): void
    {
        $this->createDeliveryServiceRestrictPackageService->create(
            deliveryServiceId: $command->deliveryServiceId,
            maxWeight: $command->maxWeight,
            maxWidth: $command->maxWidth,
            maxLength: $command->maxLength,
            maxHeight: $command->maxHeight,
        );
    }
}
