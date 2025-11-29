<?php

namespace App\Application\Shipment\Query;

use App\Application\QueryHandler;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;

readonly class GetShipmentsByPaginateQueryHandler implements QueryHandler
{
    public function __construct(public ShipmentRepositoryInterface $shipmentRepository)
    {
    }

    public function __invoke(GetShipmentsByPaginateQuery $query): array
    {
        return $this->shipmentRepository->paginate($query->getPage(), $query->getOffset());
    }
}
