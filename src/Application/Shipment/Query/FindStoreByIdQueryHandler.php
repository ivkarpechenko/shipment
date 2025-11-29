<?php

namespace App\Application\Shipment\Query;

use App\Application\QueryHandler;
use App\Domain\Shipment\Entity\Store;
use App\Domain\Shipment\Repository\StoreRepositoryInterface;

class FindStoreByIdQueryHandler implements QueryHandler
{
    public function __construct(public StoreRepositoryInterface $storeRepository)
    {
    }

    public function __invoke(FindStoreByIdQuery $query): ?Store
    {
        return $this->storeRepository->ofId($query->getStoreId());
    }
}
