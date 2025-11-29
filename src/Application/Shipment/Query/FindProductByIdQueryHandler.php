<?php

namespace App\Application\Shipment\Query;

use App\Application\QueryHandler;
use App\Domain\Shipment\Entity\Product;
use App\Domain\Shipment\Repository\ProductRepositoryInterface;

class FindProductByIdQueryHandler implements QueryHandler
{
    public function __construct(public ProductRepositoryInterface $productRepository)
    {
    }

    public function __invoke(FindProductByIdQuery $query): ?Product
    {
        return $this->productRepository->ofId($query->getProductId());
    }
}
