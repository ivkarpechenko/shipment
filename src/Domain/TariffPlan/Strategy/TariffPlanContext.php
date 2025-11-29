<?php

namespace App\Domain\TariffPlan\Strategy;

class TariffPlanContext
{
    private iterable $strategies;

    public function __construct(iterable $strategies)
    {
        $this->strategies = $strategies;
    }

    public function execute(string $deliveryServiceCode, string $tariffPlanCode): bool
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($deliveryServiceCode)) {
                return $strategy->execute($deliveryServiceCode, $tariffPlanCode);
            }
        }

        return false;
    }
}
