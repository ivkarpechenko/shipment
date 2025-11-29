<?php

namespace App\Application\DeliveryService\Query;

use App\Application\QueryHandler;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;

readonly class FindDeliveryServiceByCodeQueryHandler implements QueryHandler
{
    public function __construct(public DeliveryServiceRepositoryInterface $deliveryServiceRepository)
    {
    }

    public function __invoke(FindDeliveryServiceByCodeQuery $query): ?DeliveryService
    {
        return $this->deliveryServiceRepository->ofCode($query->getCode());
    }
}
