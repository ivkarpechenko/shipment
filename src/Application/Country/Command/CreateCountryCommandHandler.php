<?php

namespace App\Application\Country\Command;

use App\Application\CommandHandler;
use App\Domain\Country\Service\CreateCountryService;

readonly class CreateCountryCommandHandler implements CommandHandler
{
    public function __construct(public CreateCountryService $createCountryService)
    {
    }

    public function __invoke(CreateCountryCommand $command): void
    {
        $this->createCountryService->create($command->getName(), $command->getCode());
    }
}
