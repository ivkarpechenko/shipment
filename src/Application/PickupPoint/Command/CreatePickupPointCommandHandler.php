<?php

namespace App\Application\PickupPoint\Command;

use App\Application\CommandBus;
use App\Application\CommandHandler;
use App\Application\QueryBus;
use App\Domain\PickupPoint\Service\ChangePickupPointService;

readonly class CreatePickupPointCommandHandler implements CommandHandler
{
    public function __construct(
        public ChangePickupPointService $changePickupPointService,
        public CommandBus $commandBus,
        public QueryBus $queryBus
    ) {
    }

    public function __invoke(CreatePickupPointCommand $command): void
    {
        $this->changePickupPointService->change($command->dto);
    }
}
