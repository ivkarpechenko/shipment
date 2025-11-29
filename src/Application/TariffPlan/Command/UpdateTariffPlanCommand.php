<?php

namespace App\Application\TariffPlan\Command;

use App\Application\Command;

readonly class UpdateTariffPlanCommand implements Command
{
    public function __construct(
        public string $deliveryServiceCode,
        public string $deliveryMethodCode,
        public string $code,
        public ?string $name,
        public ?bool $isActive
    ) {
    }
}
