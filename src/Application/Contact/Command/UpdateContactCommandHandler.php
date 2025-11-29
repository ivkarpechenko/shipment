<?php

namespace App\Application\Contact\Command;

use App\Application\CommandHandler;
use App\Domain\Contact\Service\UpdateContactService;

readonly class UpdateContactCommandHandler implements CommandHandler
{
    public function __construct(public UpdateContactService $updateContactService)
    {
    }

    public function __invoke(UpdateContactCommand $command)
    {
        $this->updateContactService->update($command->getEmail(), $command->getName(), $command->getPhones());
    }
}
