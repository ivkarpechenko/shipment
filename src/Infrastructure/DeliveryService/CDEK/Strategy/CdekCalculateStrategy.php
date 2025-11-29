<?php

namespace App\Infrastructure\DeliveryService\CDEK\Strategy;

use App\Domain\DeliveryMethod\Enum\DeliveryMethodEnum;
use App\Domain\DeliveryService\Enum\DeliveryServiceEnum;
use App\Domain\Shipment\Dto\CalculateDto;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Enum\CargoTypeEnum;
use App\Domain\Shipment\Service\CheckShipmentService;
use App\Domain\Shipment\Strategy\CalculateStrategyInterface;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Domain\Tax\Service\CalculateTaxByCountryAndTotalSumService;
use App\Infrastructure\DeliveryService\CDEK\Service\CdekCalculateService;
use App\Infrastructure\DeliveryService\CDEK\Service\Request\Dto\CdekShipmentDto;

class CdekCalculateStrategy implements CalculateStrategyInterface
{
    protected const SUBTRACT_DAYS_FROM_MIN_PERIOD = 1;

    public function __construct(
        public CdekCalculateService $cdekCalculateService,
        public CalculateTaxByCountryAndTotalSumService $calculateTaxByCountryAndTotalSumService,
        public CheckShipmentService $checkShipmentService
    ) {
    }

    public function execute(Shipment $shipment, TariffPlan $tariffPlan): ?CalculateDto
    {
        $cdekShipmentCalculateDto = $this
            ->cdekCalculateService
            ->calculate(
                CdekShipmentDto::fromShipmentAndTariffPlan(
                    $shipment,
                    $tariffPlan
                )
            );

        $differentPsd = $shipment->getPsd()->diff(new \DateTime(date('Y-m-d')))->days;

        $minPeriod = ($cdekShipmentCalculateDto->periodMin + $differentPsd) - self::SUBTRACT_DAYS_FROM_MIN_PERIOD;

        if ($minPeriod < 1) {
            $minPeriod = 1;
        }

        return new CalculateDto(
            $minPeriod,
            $cdekShipmentCalculateDto->periodMax + $differentPsd,
            $cdekShipmentCalculateDto->deliverySum,
            $cdekShipmentCalculateDto->totalSum,
            $this->calculateTaxByCountryAndTotalSumService
                ->calculate(
                    $shipment->getTo()->getCity()->getRegion()->getCountry(),
                    $cdekShipmentCalculateDto->totalSum
                )
        );
    }

    public function supports(string $deliveryServiceCode, Shipment $shipment, TariffPlan $tariffPlan): bool
    {
        if (DeliveryServiceEnum::CDEK === DeliveryServiceEnum::from($deliveryServiceCode)) {
            if (DeliveryMethodEnum::from($tariffPlan->getDeliveryMethod()->getCode()) === DeliveryMethodEnum::PVZ) {
                return true;
            }
            $shipmentCargoTypes = $this->checkShipmentService->getCargoTypes($shipment);
            if (!empty($shipmentCargoTypes)) {
                return $this->checkShipmentService->isEqualRegion($shipment) && in_array(
                    CargoTypeEnum::SMALL_SIZED,
                    $shipmentCargoTypes
                );
            }

            return true;
        }

        return false;
    }
}
