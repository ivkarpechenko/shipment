<?php

namespace App\Application\Tax\Command;

use App\Application\CommandHandler;
use App\Domain\Tax\Service\UpdateTaxService;

readonly class UpdateTaxCommandHandler implements CommandHandler
{
    public function __construct(public UpdateTaxService $updateTaxService)
    {
    }

    public function __invoke(UpdateTaxCommand $command): void
    {
        $this->updateTaxService->update($command->getTaxId(), $command->getValue());
    }
}
