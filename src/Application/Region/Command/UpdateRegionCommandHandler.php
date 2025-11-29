<?php

namespace App\Application\Region\Command;

use App\Application\CommandHandler;
use App\Domain\Region\Service\UpdateRegionService;

readonly class UpdateRegionCommandHandler implements CommandHandler
{
    public function __construct(public UpdateRegionService $updateRegionService)
    {
    }

    public function __invoke(UpdateRegionCommand $command): void
    {
        $this->updateRegionService->update($command->getRegionId(), $command->getName(), $command->isActive());
    }
}
