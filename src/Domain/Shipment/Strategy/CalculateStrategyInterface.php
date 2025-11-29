<?php

namespace App\Domain\Shipment\Strategy;

use App\Domain\Shipment\Dto\CalculateDto;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\TariffPlan\Entity\TariffPlan;

interface CalculateStrategyInterface
{
    public function execute(Shipment $shipment, TariffPlan $tariffPlan): ?CalculateDto;

    public function supports(string $deliveryServiceCode, Shipment $shipment, TariffPlan $tariffPlan): bool;
}
