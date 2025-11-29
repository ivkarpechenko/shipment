<?php

declare(strict_types=1);

namespace App\Application\Directory\Command;

use App\Application\CommandHandler;
use App\Domain\Directory\Service\CreateOkatoOktmoService;

class CreateOkatoOktmoCommandHandler implements CommandHandler
{
    public function __construct(
        private CreateOkatoOktmoService $okatoCreateService
    ) {
    }

    public function __invoke(CreateOkatoOktmoCommand $command): void
    {
        $this->okatoCreateService->create($command->dto);
    }
}
