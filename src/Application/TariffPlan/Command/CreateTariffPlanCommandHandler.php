<?php

namespace App\Application\TariffPlan\Command;

use App\Application\CommandHandler;
use App\Domain\TariffPlan\Service\CreateTariffPlanService;

readonly class CreateTariffPlanCommandHandler implements CommandHandler
{
    public function __construct(public CreateTariffPlanService $createTariffPlanService)
    {
    }

    public function __invoke(CreateTariffPlanCommand $command): void
    {
        $this->createTariffPlanService->create(
            $command->deliveryServiceCode,
            $command->deliveryMethodCode,
            $command->code,
            $command->name
        );
    }
}
