<?php

namespace App\Domain\TariffPlan\Service;

use App\Domain\DeliveryMethod\Exception\DeliveryMethodDeactivatedException;
use App\Domain\DeliveryMethod\Exception\DeliveryMethodNotFoundException;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Exception\DeliveryServiceDeactivatedException;
use App\Domain\DeliveryService\Exception\DeliveryServiceNotFoundException;
use App\Domain\DeliveryService\Exception\DeliveryServiceNotSupportDeliveryMethodException;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Domain\TariffPlan\Exception\TariffPlanAlreadyCreatedException;
use App\Domain\TariffPlan\Exception\TariffPlanDeactivatedException;
use App\Domain\TariffPlan\Exception\TariffPlanIsNotSupportedByDeliveryServiceException;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use App\Domain\TariffPlan\Strategy\TariffPlanContext;

readonly class CreateTariffPlanService
{
    public function __construct(
        public DeliveryMethodRepositoryInterface $deliveryMethodRepository,
        public DeliveryServiceRepositoryInterface $deliveryServiceRepository,
        public TariffPlanContext $tariffPlanContext,
        public TariffPlanRepositoryInterface $tariffPlanRepository,
    ) {
    }

    public function create(string $deliveryServiceCode, string $deliveryMethodCode, string $code, string $name): void
    {
        $deliveryService = $this->deliveryServiceRepository->ofCode($deliveryServiceCode);
        if (is_null($deliveryService)) {
            $deliveryService = $this->deliveryServiceRepository->ofCodeDeactivated($deliveryServiceCode);
            if (!is_null($deliveryService)) {
                throw new DeliveryServiceDeactivatedException(sprintf('Delivery service with code %s deactivated', $deliveryServiceCode));
            }

            throw new DeliveryServiceNotFoundException(sprintf('Delivery service with code %s not found', $deliveryServiceCode));
        }

        $deliveryMethod = $this->deliveryMethodRepository->ofCode($deliveryMethodCode);
        if (is_null($deliveryMethod)) {
            $deliveryMethod = $this->deliveryMethodRepository->ofCodeDeactivated($deliveryMethodCode);
            if (!is_null($deliveryMethod)) {
                throw new DeliveryMethodDeactivatedException(sprintf('Delivery method with code %s deactivated', $deliveryMethodCode));
            }

            throw new DeliveryMethodNotFoundException(sprintf('Delivery method with code %s not found', $deliveryMethodCode));
        }

        if (!$deliveryService->getDeliveryMethods()->contains($deliveryMethod)) {
            throw new DeliveryServiceNotSupportDeliveryMethodException(
                sprintf(
                    'Delivery service with code %s does not support delivery method with code %s',
                    $deliveryService->getCode(),
                    $deliveryMethod->getCode()
                )
            );
        }

        $tariffPlan = $this->tariffPlanRepository->ofCode($deliveryServiceCode, $deliveryMethodCode, $code);
        if (!is_null($tariffPlan)) {
            throw new TariffPlanAlreadyCreatedException(sprintf(
                'Tariff plan with code %s for the delivery service %s has already been created',
                $code,
                $deliveryServiceCode
            ));
        }

        $tariffPlan = $this->tariffPlanRepository->ofCodeDeactivated($deliveryServiceCode, $deliveryMethodCode, $code);
        if (!is_null($tariffPlan)) {
            throw new TariffPlanDeactivatedException(sprintf(
                'The tariff plan with the code %s for the delivery service %s is deactivated',
                $code,
                $deliveryServiceCode
            ));
        }

        if (!$this->tariffPlanContext->execute($deliveryServiceCode, $code)) {
            throw new TariffPlanIsNotSupportedByDeliveryServiceException(sprintf(
                'The tariff plan is not supported by the delivery service %s',
                $deliveryServiceCode
            ));
        }

        $tariffPlan = new TariffPlan($deliveryService, $deliveryMethod, $code, $name);
        $this->tariffPlanRepository->create($tariffPlan);
    }
}
