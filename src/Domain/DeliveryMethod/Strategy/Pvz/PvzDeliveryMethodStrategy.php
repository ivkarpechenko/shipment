<?php

namespace App\Domain\DeliveryMethod\Strategy\Pvz;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryMethod\Enum\DeliveryMethodEnum;
use App\Domain\DeliveryMethod\Strategy\DeliveryMethodStrategyInterface;
use App\Domain\DeliveryMethod\Strategy\Dto\DeliveryMethodDto;
use App\Domain\DeliveryMethod\Strategy\Dto\DeliveryServiceDto;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\Shipment\Entity\Shipment;

class PvzDeliveryMethodStrategy implements DeliveryMethodStrategyInterface
{
    public function execute(DeliveryMethod $deliveryMethod, Shipment $shipment): ?DeliveryMethodDto
    {
        $deliveryServices = [];
        /** @var DeliveryService $deliveryService */
        foreach ($deliveryMethod->getDeliveryServices() as $deliveryService) {
            if ($deliveryService->isActive()) {
                $deliveryServices[] = new DeliveryServiceDto($deliveryService->getCode(), $deliveryService->getName());
            }
        }
        if (empty($deliveryServices)) {
            return null;
        }

        return new DeliveryMethodDto($deliveryMethod->getCode(), $deliveryMethod->getName(), $deliveryServices);
    }

    public function supports(string $deliveryMethodCode, Shipment $shipment): bool
    {
        return DeliveryMethodEnum::PVZ === DeliveryMethodEnum::from($deliveryMethodCode);
    }
}
