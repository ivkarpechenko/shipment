<?php

namespace App\Application\DeliveryService\Query;

use App\Application\QueryHandler;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictArea;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictAreaRepositoryInterface;

readonly class FindDeliveryServiceRestrictAreaByIdQueryHandler implements QueryHandler
{
    public function __construct(public DeliveryServiceRestrictAreaRepositoryInterface $deliveryServiceRestrictAreaRepository)
    {
    }

    public function __invoke(FindDeliveryServiceRestrictAreaByIdQuery $query): ?DeliveryServiceRestrictArea
    {
        return $this->deliveryServiceRestrictAreaRepository->ofId($query->getDeliveryServiceRestrictAreaId());
    }
}
