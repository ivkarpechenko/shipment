<?php

namespace App\Application\Shipment\Command;

use App\Application\CommandHandler;
use App\Application\DeliveryMethod\Query\FindDeliveryMethodByCodeDeactivatedQuery;
use App\Application\DeliveryMethod\Query\FindDeliveryMethodByCodeQuery;
use App\Application\DeliveryService\Query\FindDeliveryServiceByCodeDeactivatedQuery;
use App\Application\DeliveryService\Query\FindDeliveryServiceByCodeQuery;
use App\Application\QueryBus;
use App\Application\Shipment\Query\FindCalculateByIdQuery;
use App\Application\Shipment\Query\FindShipmentByIdQuery;
use App\Application\TariffPlan\Query\FindTariffPlanByCodeQuery;
use App\Domain\DeliveryMethod\Enum\DeliveryMethodEnum;
use App\Domain\DeliveryMethod\Exception\DeliveryMethodDeactivatedException;
use App\Domain\DeliveryService\Enum\DeliveryServiceEnum;
use App\Domain\DeliveryService\Exception\DeliveryServiceDeactivatedException;
use App\Domain\DeliveryService\Exception\DeliveryServiceNotFoundException;
use App\Domain\DeliveryService\Exception\DeliveryServiceNotSupportDeliveryMethodException;
use App\Domain\Shipment\Exception\ShipmentNotFoundException;
use App\Domain\Shipment\Service\CreateCalculateService;
use App\Domain\TariffPlan\Exception\TariffPlanNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

readonly class TekTorgCreateCalculateCommandHandler implements CommandHandler
{
    public function __construct(
        public QueryBus $queryBus,
        public CreateCalculateService $createCalculateService,
        public ParameterBagInterface $parameterBag,
        public LoggerInterface $logger
    ) {
    }

    public function __invoke(TekTorgCreateCalculateCommand $command): array
    {
        $shipment = $this->queryBus->handle(new FindShipmentByIdQuery($command->shipmentId));

        if (is_null($shipment)) {
            throw new ShipmentNotFoundException(sprintf(
                'Shipment with ID %s was not found',
                $command->shipmentId->toRfc4122()
            ));
        }

        $deliveryService = $this->queryBus->handle(new FindDeliveryServiceByCodeQuery(DeliveryServiceEnum::CDEK->value));
        if (is_null($deliveryService)) {
            $deliveryService = $this->queryBus->handle(new FindDeliveryServiceByCodeDeactivatedQuery(DeliveryServiceEnum::CDEK->value));
            if (!is_null($deliveryService)) {
                throw new DeliveryServiceDeactivatedException(sprintf('Delivery service with code %s deactivated', DeliveryServiceEnum::CDEK->value));
            }

            throw new DeliveryServiceNotFoundException(sprintf('Delivery service with code %s not found', DeliveryServiceEnum::CDEK->value));
        }

        $deliveryMethod = $this->queryBus->handle(new FindDeliveryMethodByCodeQuery(DeliveryMethodEnum::COURIER->value));
        if (is_null($deliveryMethod)) {
            $deliveryMethod = $this->queryBus->handle(new FindDeliveryMethodByCodeDeactivatedQuery(DeliveryMethodEnum::COURIER->value));
            if (!is_null($deliveryMethod)) {
                throw new DeliveryMethodDeactivatedException(sprintf('Delivery method with code %s deactivated', DeliveryMethodEnum::COURIER->value));
            }

            throw new DeliveryServiceNotFoundException(sprintf('Delivery method with code %s not found', DeliveryMethodEnum::COURIER->value));
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

        $tariffPlans = [];
        foreach ($this->parameterBag->get('available_cdek_tariffs_for_tek_torg') as $tariffCode) {
            $tariff = $this->queryBus->handle(new FindTariffPlanByCodeQuery(
                $deliveryService->getCode(),
                $deliveryMethod->getCode(),
                $tariffCode
            ));
            if (is_null($tariff)) {
                throw new TariffPlanNotFoundException(sprintf(
                    'The tariff plan with the code %s for the delivery service %s was not found',
                    $tariffCode,
                    $deliveryService->getCode()
                ));
            }
            $tariffPlans[] = $tariff;
        }

        $calculates = [];
        foreach ($tariffPlans as $tariffPlan) {
            try {
                $calculateId = $this->createCalculateService->create($shipment->getId(), $tariffPlan->getId(), $command->expiredAt);

                if (is_null($calculateId)) {
                    continue;
                }

                $calculate = $this->queryBus->handle(new FindCalculateByIdQuery($calculateId));

                $calculates[] = $calculate;
            } catch (\Throwable $exception) {
                $this->logger->critical(sprintf(
                    'Calculation error for shipment with id %s, exception: %s',
                    $shipment->getId()->toRfc4122(),
                    $exception->getMessage()
                ));
            }
        }

        return $calculates;
    }
}
