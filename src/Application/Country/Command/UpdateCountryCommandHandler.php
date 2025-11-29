<?php

namespace App\Application\Country\Command;

use App\Application\CommandHandler;
use App\Domain\Country\Service\UpdateCountryService;

readonly class UpdateCountryCommandHandler implements CommandHandler
{
    public function __construct(public UpdateCountryService $updateCountryService)
    {
    }

    public function __invoke(UpdateCountryCommand $command): void
    {
        $this->updateCountryService->update($command->getCountryId(), $command->getName(), $command->isActive());
    }
}
