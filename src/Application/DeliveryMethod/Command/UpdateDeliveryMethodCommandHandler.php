<?php

declare(strict_types=1);

namespace App\Application\DeliveryMethod\Command;

use App\Application\CommandHandler;
use App\Domain\DeliveryMethod\Service\UpdateDeliveryMethodService;

readonly class UpdateDeliveryMethodCommandHandler implements CommandHandler
{
    public function __construct(
        public UpdateDeliveryMethodService $updateDeliveryMethodService,
    ) {
    }

    public function __invoke(UpdateDeliveryMethodCommand $command): void
    {
        $this->updateDeliveryMethodService->update($command->code, $command->name, $command->isActive);
    }
}
