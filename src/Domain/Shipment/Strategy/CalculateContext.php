<?php

namespace App\Domain\Shipment\Strategy;

use App\Domain\Shipment\Dto\CalculateDto;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\TariffPlan\Entity\TariffPlan;

class CalculateContext
{
    private iterable $strategies;

    public function __construct(iterable $strategies)
    {
        $this->strategies = $strategies;
    }

    public function execute(Shipment $shipment, TariffPlan $tariffPlan): ?CalculateDto
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($tariffPlan->getDeliveryService()->getCode(), $shipment, $tariffPlan)) {
                return $strategy->execute($shipment, $tariffPlan);
            }
        }

        return null;
    }
}
