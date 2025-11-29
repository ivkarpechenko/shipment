<?php

namespace App\Application\Address\Command;

use App\Application\CommandHandler;
use App\Domain\Address\Service\RestoreAddressService;

readonly class RestoreAddressCommandHandler implements CommandHandler
{
    public function __construct(public RestoreAddressService $restoreAddressService)
    {
    }

    public function __invoke(RestoreAddressCommand $command): void
    {
        $this->restoreAddressService->restore($command->getAddressId());
    }
}
