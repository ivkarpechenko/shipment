<?php

namespace App\Application\Tax\Command;

use App\Application\CommandHandler;
use App\Domain\Tax\Service\RestoreTaxService;

readonly class RestoreTaxCommandHandler implements CommandHandler
{
    public function __construct(public RestoreTaxService $restoreTaxService)
    {
    }

    public function __invoke(RestoreTaxCommand $command): void
    {
        $this->restoreTaxService->restore($command->getTaxId());
    }
}
