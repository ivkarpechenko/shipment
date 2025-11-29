<?php

namespace App\Application\Region\Command;

use App\Application\CommandHandler;
use App\Domain\Region\Service\RestoreRegionService;

readonly class RestoreRegionCommandHandler implements CommandHandler
{
    public function __construct(public RestoreRegionService $restoreRegionService)
    {
    }

    public function __invoke(RestoreRegionCommand $command): void
    {
        $this->restoreRegionService->restore($command->getRegionId());
    }
}
