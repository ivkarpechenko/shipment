<?php

namespace App\Application\City\Command;

use App\Application\CommandHandler;
use App\Domain\City\Service\DeleteCityService;

readonly class DeleteCityCommandHandler implements CommandHandler
{
    public function __construct(public DeleteCityService $deleteCityService)
    {
    }

    public function __invoke(DeleteCityCommand $command): void
    {
        $this->deleteCityService->delete($command->getCityId());
    }
}
