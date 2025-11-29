<?php

namespace App\Application\Currency\Command;

use App\Application\CommandHandler;
use App\Domain\Currency\Service\CreateCurrencyService;

readonly class CreateCurrencyCommandHandler implements CommandHandler
{
    public function __construct(public CreateCurrencyService $createCurrencyService)
    {
    }

    public function __invoke(CreateCurrencyCommand $command): void
    {
        $this->createCurrencyService->create($command->getCode(), $command->getNum(), $command->getName());
    }
}
