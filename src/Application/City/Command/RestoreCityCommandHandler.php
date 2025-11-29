<?php

namespace App\Application\City\Command;

use App\Application\CommandHandler;
use App\Domain\City\Service\RestoreCityService;

readonly class RestoreCityCommandHandler implements CommandHandler
{
    public function __construct(public RestoreCityService $restoreCityService)
    {
    }

    public function __invoke(RestoreCityCommand $command): void
    {
        $this->restoreCityService->restore($command->getCityId());
    }
}
