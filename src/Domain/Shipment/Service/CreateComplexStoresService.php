<?php

namespace App\Domain\Shipment\Service;

use App\Domain\Shipment\Entity\Product;
use App\Domain\Shipment\Entity\Store;
use App\Domain\Shipment\Repository\ProductRepositoryInterface;

readonly class CreateComplexStoresService
{
    public function __construct(public ProductRepositoryInterface $productRepository)
    {
    }

    public function create(array $stores): array
    {
        $stores = array_map(function (Store $store) {
            return $store->getId();
        }, $stores);

        if (empty($stores)) {
            return $stores;
        }

        $products = $this->productRepository->ofStores($stores);

        $stores = [];
        array_walk($products, function (Product $product) use (&$stores) {
            $stores[sprintf('%s:%s', $product->getStore()->getExternalId(), $product->getDeliveryPeriod())] = $product->getStore();
        });

        return $stores;
    }
}
