<?php

namespace App\Domain\Shipment\Service\Packing;

use App\Domain\Shipment\Entity\Product;

final class BoxCapacity
{
    private float $maxWeight;

    private float $remainingWeight;

    public function __construct(float $maxWeight)
    {
        $this->maxWeight = $maxWeight;
        $this->remainingWeight = $maxWeight;
    }

    public function getUsedWeight(): float
    {
        return $this->maxWeight - $this->remainingWeight;
    }

    public function canAddProduct(Product $product): bool
    {
        return $product->getWeight() <= $this->remainingWeight;
    }

    public function updateCapacity(Product $product): void
    {
        $this->remainingWeight -= $product->getWeight();
    }
}
