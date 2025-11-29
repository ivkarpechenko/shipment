<?php

declare(strict_types=1);

namespace App\Application\DeliveryMethod\Query;

use App\Application\QueryBus;
use App\Application\QueryHandler;
use App\Application\Shipment\Query\FindShipmentByIdQuery;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryMethod\Strategy\DeliveryMethodContext;
use App\Domain\Shipment\Exception\ShipmentNotFoundException;

readonly class FindDeliveryMethodByShipmentIdQueryHandler implements QueryHandler
{
    public function __construct(
        public DeliveryMethodRepositoryInterface $deliveryMethodRepository,
        public DeliveryMethodContext $deliveryMethodContext,
        public QueryBus $queryBus
    ) {
    }

    public function __invoke(FindDeliveryMethodByShipmentIdQuery $query): ?array
    {
        $shipment = $this->queryBus->handle(new FindShipmentByIdQuery($query->shipmentId));

        if (is_null($shipment)) {
            throw new ShipmentNotFoundException(sprintf(
                'Shipment with ID %s was not found',
                $query->shipmentId->toRfc4122()
            ));
        }

        $deliveryMethods = $this->deliveryMethodRepository->all(true);
        $deliveryMethodsDto = [];
        foreach ($deliveryMethods as $deliveryMethod) {
            $deliveryMethodDto = $this->deliveryMethodContext->execute($deliveryMethod, $shipment);
            if (!is_null($deliveryMethodDto)) {
                $deliveryMethodsDto[] = $deliveryMethodDto;
            }
        }

        return $deliveryMethodsDto;
    }
}
