<?php

namespace App\Application\TariffPlan\Command;

use App\Application\CommandHandler;
use App\Domain\TariffPlan\Service\UpdateTariffPlanService;

readonly class UpdateTariffPlanCommandHandler implements CommandHandler
{
    public function __construct(public UpdateTariffPlanService $updateTariffPlanService)
    {
    }

    public function __invoke(UpdateTariffPlanCommand $command): void
    {
        $this->updateTariffPlanService->update(
            $command->deliveryServiceCode,
            $command->deliveryMethodCode,
            $command->code,
            $command->name,
            $command->isActive
        );
    }
}
