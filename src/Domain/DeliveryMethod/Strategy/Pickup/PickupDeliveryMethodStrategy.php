<?php

namespace App\Domain\DeliveryMethod\Strategy\Pickup;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryMethod\Enum\DeliveryMethodEnum;
use App\Domain\DeliveryMethod\Strategy\DeliveryMethodStrategyInterface;
use App\Domain\DeliveryMethod\Strategy\Dto\DeliveryMethodDto;
use App\Domain\DeliveryMethod\Strategy\Dto\DeliveryServiceDto;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Service\CheckShipmentService;

class PickupDeliveryMethodStrategy implements DeliveryMethodStrategyInterface
{
    public function __construct(public CheckShipmentService $checkShipmentService)
    {
    }

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
        if (DeliveryMethodEnum::PICKUP === DeliveryMethodEnum::from($deliveryMethodCode)
            && $this->checkShipmentService->isStoresAllowedPickup($shipment)
            && $this->checkShipmentService->isEqualRegion($shipment)
        ) {
            return true;
        }

        return false;
    }
}
