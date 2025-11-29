<?php

namespace App\Application\Region\Command;

use App\Application\CommandHandler;
use App\Domain\Region\Service\CreateRegionService;

readonly class CreateRegionCommandHandler implements CommandHandler
{
    public function __construct(public CreateRegionService $createRegionService)
    {
    }

    public function __invoke(CreateRegionCommand $command): void
    {
        $this->createRegionService->create($command->getCountryCode(), $command->getName(), $command->getCode());
    }
}
