<?php

namespace App\Domain\Shipment\Service;

use App\Domain\DeliveryMethod\Enum\DeliveryMethodEnum;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\Shipment\Entity\Calculate;
use App\Domain\Shipment\Exception\ShipmentNotFoundException;
use App\Domain\Shipment\Repository\CalculateRepositoryInterface;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use App\Domain\Shipment\Strategy\CalculateContext;
use App\Domain\TariffPlan\Exception\TariffPlanNotFoundActiveException;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class CreateCalculateService
{
    public function __construct(
        public ShipmentRepositoryInterface $shipmentRepository,
        public DeliveryServiceRepositoryInterface $deliveryServiceRepository,
        public DeliveryMethodRepositoryInterface $deliveryMethodRepository,
        public TariffPlanRepositoryInterface $tariffPlanRepository,
        public CalculateRepositoryInterface $calculateRepository,
        public CalculateContext $calculateContext,
        public CheckAddressInRestrictedAreaService $addressInRestrictedAreaService
    ) {
    }

    public function create(Uuid $shipmentId, Uuid $tariffPlanId, ?\DateTime $expiredAt = null): ?Uuid
    {
        $shipment = $this->shipmentRepository->ofId($shipmentId);
        if (is_null($shipment)) {
            throw new ShipmentNotFoundException(sprintf('Shipment with ID %s was not found', $shipmentId->toRfc4122()));
        }

        $tariffPlan = $this->tariffPlanRepository->ofId($tariffPlanId);
        if (is_null($tariffPlan)) {
            throw new TariffPlanNotFoundActiveException(sprintf('TariffPlan with ID %s was not found', $tariffPlanId->toRfc4122()));
        }

        // рассчет активен - не создаем новый
        $calculate = $this->calculateRepository->ofShipmentAndTariffPlanIdNotExpired($shipmentId, $tariffPlanId);
        if (!is_null($calculate)
            && DeliveryMethodEnum::from($tariffPlan->getDeliveryMethod()->getCode()) !== DeliveryMethodEnum::PVZ
        ) {
            return $calculate->getId();
        }

        $calculateDto = $this->calculateContext->execute($shipment, $tariffPlan);

        if (is_null($calculateDto)) {
            return null;
        }

        if ($this->addressInRestrictedAreaService->check(
            $tariffPlan->getDeliveryService()->getId(),
            $shipment->getFrom()->getId()
        )) {
            return null;
        }

        if ($this->addressInRestrictedAreaService->check(
            $tariffPlan->getDeliveryService()->getId(),
            $shipment->getTo()->getId()
        )) {
            return null;
        }

        if (!is_null($calculate)) {
            $calculate->change($calculateDto);

            return $this->calculateRepository->update($calculate);
        }

        $calculate = new Calculate(
            $shipment,
            $tariffPlan,
            $calculateDto->minPeriod,
            $calculateDto->maxPeriod,
            $calculateDto->deliveryCost,
            $calculateDto->deliveryTotalCost,
            $calculateDto->deliveryTotalCostTax
        );

        if ($expiredAt) {
            $calculate->changeExpiredAt($expiredAt);
        }

        return $this->calculateRepository->create($calculate);
    }
}
