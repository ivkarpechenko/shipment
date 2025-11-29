<?php

namespace App\Application\Shipment\Query;

use App\Application\QueryHandler;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;

readonly class FindShipmentByIdQueryHandler implements QueryHandler
{
    public function __construct(public ShipmentRepositoryInterface $shipmentRepository)
    {
    }

    public function __invoke(FindShipmentByIdQuery $query): ?Shipment
    {
        return $this->shipmentRepository->ofId($query->getShipmentId());
    }
}
