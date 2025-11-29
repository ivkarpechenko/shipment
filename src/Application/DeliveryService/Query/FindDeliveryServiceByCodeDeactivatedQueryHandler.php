<?php

namespace App\Application\DeliveryService\Query;

use App\Application\QueryHandler;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;

readonly class FindDeliveryServiceByCodeDeactivatedQueryHandler implements QueryHandler
{
    public function __construct(public DeliveryServiceRepositoryInterface $deliveryServiceRepository)
    {
    }

    public function __invoke(FindDeliveryServiceByCodeDeactivatedQuery $query): ?DeliveryService
    {
        return $this->deliveryServiceRepository->ofCodeDeactivated($query->getCode());
    }
}
