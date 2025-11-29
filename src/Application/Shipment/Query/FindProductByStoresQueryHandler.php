<?php

namespace App\Application\Shipment\Query;

use App\Application\QueryHandler;
use App\Domain\Shipment\Repository\ProductRepositoryInterface;

readonly class FindProductByStoresQueryHandler implements QueryHandler
{
    public function __construct(public ProductRepositoryInterface $productRepository)
    {
    }

    public function __invoke(FindProductByStoresQuery $query): array
    {
        return $this->productRepository->ofStores($query->getStores());
    }
}
