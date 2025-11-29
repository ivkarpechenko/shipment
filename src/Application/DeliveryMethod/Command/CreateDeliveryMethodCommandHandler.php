<?php

declare(strict_types=1);

namespace App\Application\DeliveryMethod\Command;

use App\Application\CommandHandler;
use App\Domain\DeliveryMethod\Service\CreateDeliveryMethodService;

readonly class CreateDeliveryMethodCommandHandler implements CommandHandler
{
    public function __construct(
        public CreateDeliveryMethodService $createDeliveryMethodService
    ) {
    }

    public function __invoke(CreateDeliveryMethodCommand $command): void
    {
        $this->createDeliveryMethodService->create($command->code, $command->name);
    }
}
