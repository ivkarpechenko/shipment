<?php

namespace App\Application\TariffPlan\Query;

use App\Application\Query;

readonly class FindTariffPlanByServiceAndMethodQuery implements Query
{
    public function __construct(
        public string $deliveryServiceCode,
        public string $deliveryMethodCode
    ) {
    }
}
