<?php

namespace App\Application\Tax\Command;

use App\Application\CommandHandler;
use App\Domain\Tax\Service\CreateTaxService;

readonly class CreateTaxCommandHandler implements CommandHandler
{
    public function __construct(public CreateTaxService $createTaxService)
    {
    }

    public function __invoke(CreateTaxCommand $command): void
    {
        $this->createTaxService->create($command->getCountryCode(), $command->getName(), $command->getValue(), $command->getExpression());
    }
}
