<?php

namespace App\Application\City\Command;

use App\Application\CommandHandler;
use App\Domain\City\Service\CreateCityService;

readonly class CreateCityCommandHandler implements CommandHandler
{
    public function __construct(public CreateCityService $createCityService)
    {
    }

    public function __invoke(CreateCityCommand $command): void
    {
        $this->createCityService->create($command->getRegionCode(), $command->getType(), $command->getName());
    }
}
