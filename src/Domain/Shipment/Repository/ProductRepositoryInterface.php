<?php

namespace App\Domain\Shipment\Repository;

use App\Domain\Shipment\Entity\Product;
use Symfony\Component\Uid\Uuid;

interface ProductRepositoryInterface
{
    public function create(Product $product): Uuid;

    public function ofId(Uuid $productId): ?Product;

    public function ofStores(array $stores): array;

    public function ofStoreAndDeliveryPeriod(Uuid $storeId, int $deliveryPeriod): array;
}
