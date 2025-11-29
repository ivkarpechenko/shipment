<?php

namespace App\Application\Shipment\Query;

use App\Application\QueryHandler;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;

readonly class FindShipmentsByQueryHandler implements QueryHandler
{
    public function __construct(public ShipmentRepositoryInterface $shipmentRepository)
    {
    }

    public function __invoke(FindShipmentsByQuery $query): array
    {
        return $this->shipmentRepository->ofShipments($query->getShipments());
    }
}
