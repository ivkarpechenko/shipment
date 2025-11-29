<?php

namespace App\Application\Address\Command;

use App\Application\CommandHandler;
use App\Domain\Address\Service\UpdateAddressService;

readonly class UpdateAddressCommandHandler implements CommandHandler
{
    public function __construct(public UpdateAddressService $updateAddressService)
    {
    }

    public function __invoke(UpdateAddressCommand $command): void
    {
        $this->updateAddressService->update($command->getAddressId(), $command->getIsActive());
    }
}
