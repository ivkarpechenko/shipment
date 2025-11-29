<?php

namespace App\Application\Country\Command;

use App\Application\CommandHandler;
use App\Domain\Country\Service\RestoreCountryService;

readonly class RestoreCountryCommandHandler implements CommandHandler
{
    public function __construct(public RestoreCountryService $restoreCountryService)
    {
    }

    public function __invoke(RestoreCountryCommand $command): void
    {
        $this->restoreCountryService->restore($command->getCountryId());
    }
}
