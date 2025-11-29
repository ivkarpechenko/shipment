<?php

namespace App\Domain\Shipment\Service\Packing;

use App\Domain\Shipment\Entity\PackageProduct;
use App\Domain\Shipment\Service\Packing\Strategy\PackingStrategyInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class Box
{
    private PackingStrategyInterface $strategy;

    // TODO: change collection to array with unique check
    private Collection $packedProducts;

    private float $weight = 0.0;

    private int $width = 0;

    private int $length = 0;

    private int $height = 0;

    public function __construct(PackingStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
        $this->packedProducts = new ArrayCollection();
    }

    public function getStrategy(): PackingStrategyInterface
    {
        return $this->strategy;
    }

    public function getPackedProducts(): Collection
    {
        return $this->packedProducts;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function addProduct(PackageProduct $packageProduct, ?BoxCapacity $boxCapacity = null, ?BoxDimensions $boxDimensions = null): void
    {
        $packageProduct->incrementQuantity();

        if (!$this->packedProducts->contains($packageProduct)) {
            $this->packedProducts[] = $packageProduct;
        }

        if ($boxCapacity) {
            $this->weight = $boxCapacity->getUsedWeight();
        }

        if ($boxDimensions) {
            $this->width = $boxDimensions->getUsedWidth();
            $this->length = $boxDimensions->getUsedLength();
            $this->height = $boxDimensions->getUsedHeight();
        }
    }

    public function changeWeight(float $weight): void
    {
        $this->weight = $weight;
    }

    public function changeWidth(int $width): void
    {
        $this->width = $width;
    }

    public function changeLength(int $length): void
    {
        $this->length = $length;
    }

    public function changeHeight(int $height): void
    {
        $this->height = $height;
    }

    public function getVolume(): int
    {
        return $this->width * $this->height * $this->length;
    }

    public function getSumDimensions(): int
    {
        return $this->width + $this->height + $this->length;
    }
}
