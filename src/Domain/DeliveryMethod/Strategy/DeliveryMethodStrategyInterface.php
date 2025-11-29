<?php

namespace App\Domain\DeliveryMethod\Strategy;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryMethod\Strategy\Dto\DeliveryMethodDto;
use App\Domain\Shipment\Entity\Shipment;

interface DeliveryMethodStrategyInterface
{
    public function execute(DeliveryMethod $deliveryMethod, Shipment $shipment): ?DeliveryMethodDto;

    public function supports(string $deliveryMethodCode, Shipment $shipment): bool;
}
