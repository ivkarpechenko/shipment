<?php

namespace App\Application\DeliveryService\Query;

use App\Application\QueryHandler;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;

readonly class GetDeliveryServicesByPaginateQueryHandler implements QueryHandler
{
    public function __construct(public DeliveryServiceRepositoryInterface $deliveryServiceRepository)
    {
    }

    public function __invoke(GetDeliveryServicesByPaginateQuery $query): array
    {
        return $this->deliveryServiceRepository->paginate($query->getPage(), $query->getOffset());
    }
}
