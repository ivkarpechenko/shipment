<?php

namespace App\Application\Shipment\Query;

use App\Application\QueryHandler;
use App\Domain\Shipment\Repository\CalculateRepositoryInterface;

readonly class FindCalculateByShipmentIdQueryHandler implements QueryHandler
{
    public function __construct(public CalculateRepositoryInterface $calculateRepository)
    {
    }

    public function __invoke(FindCalculateByShipmentIdQuery $query): array
    {
        return $this->calculateRepository->ofShipmentIdNotExpired($query->getShipmentId());
    }
}
