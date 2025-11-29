<?php

namespace App\Domain\Shipment\Service\Packing\Strategy;

use App\Domain\Shipment\Entity\Product;
use App\Domain\Shipment\Service\Packing\Box;

interface PackingStrategyInterface
{
    public function canAddProduct(Box $box, Product $product): bool;
}
