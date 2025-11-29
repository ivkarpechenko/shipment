<?php

declare(strict_types=1);

namespace App\Application\DeliveryService\Command;

use App\Application\CommandHandler;
use App\Domain\DeliveryService\Service\UpdateDeliveryServiceRestrictPackageService;

readonly class UpdateDeliveryServiceRestrictPackageCommandHandler implements CommandHandler
{
    public function __construct(
        public UpdateDeliveryServiceRestrictPackageService $updateDeliveryServiceRestrictPackageService
    ) {
    }

    public function __invoke(UpdateDeliveryServiceRestrictPackageCommand $command): void
    {
        $this->updateDeliveryServiceRestrictPackageService->update(
            id: $command->id,
            maxWeight: $command->maxWeight,
            maxWidth: $command->maxWidth,
            maxHeight: $command->maxHeight,
            maxLength: $command->maxLength,
            isActive: $command->isActive,
        );
    }
}
