<?php

namespace App\Application\Tax\Command;

use App\Application\CommandHandler;
use App\Domain\Tax\Service\DeleteTaxService;

readonly class DeleteTaxCommandHandler implements CommandHandler
{
    public function __construct(public DeleteTaxService $deleteTaxService)
    {
    }

    public function __invoke(DeleteTaxCommand $command): void
    {
        $this->deleteTaxService->delete($command->getTaxId());
    }
}
