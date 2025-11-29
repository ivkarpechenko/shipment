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
use App\Application\TariffPlan\Query\FindTariffPlanByServiceAndMethodQuery;
use App\Domain\DeliveryMethod\Exception\DeliveryMethodDeactivatedException;
use App\Domain\DeliveryService\Exception\DeliveryServiceDeactivatedException;
use App\Domain\DeliveryService\Exception\DeliveryServiceNotFoundException;
use App\Domain\DeliveryService\Exception\DeliveryServiceNotSupportDeliveryMethodException;
use App\Domain\Shipment\Exception\ShipmentNotFoundException;
use App\Domain\Shipment\Service\CreateCalculateService;
use Psr\Log\LoggerInterface;

readonly class CreateCalculateCommandHandler implements CommandHandler
{
    public function __construct(
        public QueryBus $queryBus,
        public CreateCalculateService $createCalculateService,
        public LoggerInterface $logger
    ) {
    }

    public function __invoke(CreateCalculateCommand $command): array
    {
        $shipment = $this->queryBus->handle(new FindShipmentByIdQuery($command->shipmentId));

        if (is_null($shipment)) {
            throw new ShipmentNotFoundException(sprintf(
                'Shipment with ID %s was not found',
                $command->shipmentId->toRfc4122()
            ));
        }

        $deliveryService = $this->queryBus->handle(new FindDeliveryServiceByCodeQuery($command->deliveryServiceCode));
        if (is_null($deliveryService)) {
            $deliveryService = $this->queryBus->handle(new FindDeliveryServiceByCodeDeactivatedQuery($command->deliveryServiceCode));
            if (!is_null($deliveryService)) {
                throw new DeliveryServiceDeactivatedException(sprintf('Delivery service with code %s deactivated', $command->deliveryServiceCode));
            }

            throw new DeliveryServiceNotFoundException(sprintf('Delivery service with code %s not found', $command->deliveryServiceCode));
        }

        $deliveryMethod = $this->queryBus->handle(new FindDeliveryMethodByCodeQuery($command->deliveryMethodCode));
        if (is_null($deliveryMethod)) {
            $deliveryMethod = $this->queryBus->handle(new FindDeliveryMethodByCodeDeactivatedQuery($command->deliveryMethodCode));
            if (!is_null($deliveryMethod)) {
                throw new DeliveryMethodDeactivatedException(sprintf('Delivery method with code %s deactivated', $command->deliveryMethodCode));
            }

            throw new DeliveryServiceNotFoundException(sprintf('Delivery method with code %s not found', $command->deliveryMethodCode));
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

        if (!$deliveryService->getDeliveryMethods()->contains($deliveryMethod)) {
            throw new DeliveryServiceNotSupportDeliveryMethodException(
                sprintf(
                    'Delivery service with code %s does not support delivery method with code %s',
                    $deliveryService->getCode(),
                    $deliveryMethod->getCode()
                )
            );
        }

        $tariffPlans = $this->queryBus->handle(new FindTariffPlanByServiceAndMethodQuery($deliveryService->getCode(), $deliveryMethod->getCode()));

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
