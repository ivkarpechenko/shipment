<?php

namespace App\Domain\Shipment\Service\Packing;

use App\Domain\Shipment\Entity\Product;
use App\Domain\Shipment\Service\Packing\Enum\SideEnum;

final class BoxDimensions
{
    private int $maxWidth;

    private int $maxLength;

    private int $maxHeight;

    private int $usedWidth = 0;

    private int $usedLength = 0;

    private int $usedHeight = 0;

    public function __construct(int $maxWidth, int $maxLength, int $maxHeight)
    {
        $this->maxWidth = $maxWidth;
        $this->maxLength = $maxLength;
        $this->maxHeight = $maxHeight;
    }

    public function getUsedWidth(): int
    {
        return $this->usedWidth;
    }

    public function getUsedLength(): int
    {
        return $this->usedLength;
    }

    public function getUsedHeight(): int
    {
        return $this->usedHeight;
    }

    public function canAddProduct(Product $product): bool
    {
        $length = $width = $height = 0;

        $minSide = $this->findMinSide($product);

        match ($minSide) {
            SideEnum::LENGTH => $length += $product->getLength(),
            SideEnum::WIDTH => $width += $product->getWidth(),
            SideEnum::HEIGHT => $height += $product->getHeight()
        };

        return $this->usedWidth + $width <= $this->maxWidth
            && $this->usedLength + $length <= $this->maxLength
            && $this->usedHeight + $height <= $this->maxHeight;
    }

    public function updateUsedSide(Product $product): void
    {
        if ($this->usedWidth === 0 && $this->usedLength === 0 && $this->usedHeight === 0) {
            $this->usedWidth = $product->getWidth();
            $this->usedLength = $product->getLength();
            $this->usedHeight = $product->getHeight();
        } else {
            $minSide = $this->findMinSide($product);

            // Update the minimum side
            match ($minSide) {
                SideEnum::LENGTH => $this->usedLength += $product->getLength(),
                SideEnum::WIDTH => $this->usedWidth += $product->getWidth(),
                SideEnum::HEIGHT => $this->usedHeight += $product->getHeight()
            };
        }
    }

    private function findMinSide(Product $product): SideEnum
    {
        // Double the sizes used
        $doubledWidth = $this->usedWidth + $product->getWidth();
        $doubledLength = $this->usedLength + $product->getLength();
        $doubledHeight = $this->usedHeight + $product->getHeight();

        // Finding the minimum double side
        $minDoubledSide = min($doubledWidth, $doubledLength, $doubledHeight);

        if ($minDoubledSide === $doubledLength) {
            return SideEnum::LENGTH;
        }
        if ($minDoubledSide === $doubledWidth) {
            return SideEnum::WIDTH;
        }

        return SideEnum::HEIGHT;
    }
}
