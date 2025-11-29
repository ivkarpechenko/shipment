<?php

namespace App\Application\Country\Command;

use App\Application\CommandHandler;
use App\Domain\Country\Service\DeleteCountryService;

readonly class DeleteCountryCommandHandler implements CommandHandler
{
    public function __construct(public DeleteCountryService $deleteCountryService)
    {
    }

    public function __invoke(DeleteCountryCommand $command): void
    {
        $this->deleteCountryService->delete($command->getCountryId());
    }
}
