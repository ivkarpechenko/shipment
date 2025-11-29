<?php

namespace App\Application\DeliveryService\Query;

use App\Application\QueryHandler;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;

readonly class GetAllDeliveryServicesQueryHandler implements QueryHandler
{
    public function __construct(public DeliveryServiceRepositoryInterface $deliveryServiceRepository)
    {
    }

    public function __invoke(GetAllDeliveryServicesQuery $query): array
    {
        return $this->deliveryServiceRepository->all($query->getIsActive());
    }
}
