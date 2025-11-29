<?php

namespace App\Application\Shipment\Query;

use App\Application\QueryHandler;
use App\Domain\Shipment\Repository\ProductRepositoryInterface;

readonly class FindProductByStoreAndDeliveryPeriodQueryHandler implements QueryHandler
{
    public function __construct(public ProductRepositoryInterface $productRepository)
    {
    }

    public function __invoke(FindProductByStoreAndDeliveryPeriodQuery $query): array
    {
        return $this->productRepository->ofStoreAndDeliveryPeriod(
            $query->getStoreId(),
            $query->getDeliveryPeriod()
        );
    }
}
