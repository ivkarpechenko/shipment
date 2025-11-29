<?php

namespace App\Application\TariffPlan\Query;

use App\Application\Query;

readonly class FindTariffPlanByDeliveryServiceCodeQuery implements Query
{
    public function __construct(private string $deliveryServiceCode)
    {
    }

    public function getDeliveryServiceCode(): string
    {
        return $this->deliveryServiceCode;
    }
}
