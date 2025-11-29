<?php

namespace App\Domain\Shipment\Service\Packing\Strategy;

use App\Domain\Shipment\Entity\Product;
use App\Domain\Shipment\Service\Packing\Box;

class FragilePackingStrategy implements PackingStrategyInterface
{
    public function canAddProduct(Box $box, Product $product): bool
    {
        return $product->isFragile() && !$product->isFlammable();
    }
}
