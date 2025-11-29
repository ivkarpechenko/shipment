<?php

namespace App\Application\Contact\Command;

use App\Application\CommandHandler;
use App\Domain\Contact\Service\CreateContactService;

readonly class CreateContactCommandHandler implements CommandHandler
{
    public function __construct(public CreateContactService $createContactService)
    {
    }

    public function __invoke(CreateContactCommand $command)
    {
        $this->createContactService->create($command->getEmail(), $command->getName(), $command->getPhones());
    }
}
