<?php

namespace App\Application\Region\Command;

use App\Application\CommandHandler;
use App\Domain\Region\Service\DeleteRegionService;

readonly class DeleteRegionCommandHandler implements CommandHandler
{
    public function __construct(public DeleteRegionService $deleteRegionService)
    {
    }

    public function __invoke(DeleteRegionCommand $command): void
    {
        $this->deleteRegionService->delete($command->getRegionId());
    }
}
