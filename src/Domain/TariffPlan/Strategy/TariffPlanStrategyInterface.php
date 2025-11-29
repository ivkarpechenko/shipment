<?php

namespace App\Domain\TariffPlan\Strategy;

interface TariffPlanStrategyInterface
{
    public function execute(string $deliveryServiceCode, string $tariffPlanCode): bool;

    public function supports(string $deliveryServiceCode): bool;
}
