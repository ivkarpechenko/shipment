<?php

namespace App\Domain\DeliveryMethod\Strategy;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryMethod\Strategy\Dto\DeliveryMethodDto;
use App\Domain\Shipment\Entity\Shipment;

class DeliveryMethodContext
{
    private iterable $strategies;

    public function __construct(iterable $strategies)
    {
        $this->strategies = $strategies;
    }

    public function execute(DeliveryMethod $deliveryMethod, Shipment $shipment): ?DeliveryMethodDto
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($deliveryMethod->getCode(), $shipment)) {
                return $strategy->execute($deliveryMethod, $shipment);
            }
        }

        return null;
    }
}
