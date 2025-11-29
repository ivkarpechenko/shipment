<?php

namespace App\Application\DeliveryService\Query;

use App\Application\QueryHandler;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictAreaRepositoryInterface;

readonly class FindDeliveryServiceRestrictAreaByDeliveryServiceIdQueryHandler implements QueryHandler
{
    public function __construct(public DeliveryServiceRestrictAreaRepositoryInterface $restrictAreaRepository)
    {
    }

    public function __invoke(FindDeliveryServiceRestrictAreaByDeliveryServiceIdQuery $query): array
    {
        return $this->restrictAreaRepository->ofDeliveryServiceId($query->getDeliveryServiceId());
    }
}
