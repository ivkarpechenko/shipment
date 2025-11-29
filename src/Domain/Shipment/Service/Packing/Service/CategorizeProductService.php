<?php

namespace App\Domain\Shipment\Service\Packing\Service;

use App\Domain\Shipment\Entity\Product;
use App\Domain\Shipment\Service\Packing\Enum\CategoryEnum;

readonly class CategorizeProductService
{
    public function categorize(
        array $products,
        float $boxMaxWeight,
        int $boxMaxHeight,
        int $boxMaxWidth,
        int $boxMaxLength
    ): array {
        $categories = [];

        /** @var Product $product */
        foreach ($products as $product) {
            if ($product->isCanRotate()) {
                $product = $this->rotateProduct($product);
            }

            if ($product->isFragile() && !$product->isFlammable()) {
                // Selection of fragile products
                $categories[CategoryEnum::FRAGILE->value][] = $product;
            } elseif ($product->isFlammable()) {
                // Selection of flammable products
                $categories[CategoryEnum::FLAMMABLE->value][] = $product;
            } elseif (
                $product->getWeight() > $boxMaxWeight
                || $product->getHeight() > $boxMaxHeight
                || $product->getWidth() > $boxMaxWidth
                || $product->getLength() > $boxMaxLength
            ) {
                // Selection heavy and large products
                $categories[CategoryEnum::LARGE_OR_HEAVY->value][] = $product;
            } else {
                // Selection regular products
                $categories[CategoryEnum::REGULAR->value][] = $product;
            }
        }

        return $categories;
    }

    protected function rotateProduct(Product $product): Product
    {
        $dimensions = [$product->getLength(), $product->getWidth(), $product->getHeight()];

        sort($dimensions);

        $product->changeHeight($dimensions[0]);
        $product->changeWidth($dimensions[1]);
        $product->changeLength($dimensions[2]);

        return $product;
    }
}
