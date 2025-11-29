<?php

namespace App\Application\Address\Command;

use App\Application\CommandHandler;
use App\Domain\Address\Service\DeleteAddressService;

readonly class DeleteAddressCommandHandler implements CommandHandler
{
    public function __construct(public DeleteAddressService $deleteAddressService)
    {
    }

    public function __invoke(DeleteAddressCommand $command): void
    {
        $this->deleteAddressService->delete($command->getAddressId());
    }
}
