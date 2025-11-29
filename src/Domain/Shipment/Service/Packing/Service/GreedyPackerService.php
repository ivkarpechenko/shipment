<?php

namespace App\Domain\Shipment\Service\Packing\Service;

use App\Domain\Shipment\Entity\PackageProduct;
use App\Domain\Shipment\Entity\Product;
use App\Domain\Shipment\Service\Packing\Box;
use App\Domain\Shipment\Service\Packing\BoxCapacity;
use App\Domain\Shipment\Service\Packing\BoxDimensions;
use App\Domain\Shipment\Service\Packing\Factory\BoxFactoryInterface;

readonly class GreedyPackerService
{
    /**
     * @param Product[] $products
     * @return Box[]
     */
    public function pack(
        BoxFactoryInterface $boxFactory,
        array $products,
        // in grams
        float $boxMaxWeight,
        // in millimeters
        int $boxMaxHeight,
        // in millimeters
        int $boxMaxWidth,
        // in millimeters
        int $boxMaxLength
    ): array {
        // Sort products in descending order of volume (greedy step)
        usort($products, function (Product $a, Product $b) {
            return $b->getVolume() - $a->getVolume();
        });

        $boxes = [];
        $currentBox = $boxFactory->createBox();
        $boxCapacity = new BoxCapacity($boxMaxWeight);
        $boxDimensions = new BoxDimensions($boxMaxWidth, $boxMaxLength, $boxMaxHeight);

        $boxes[] = $currentBox;
        foreach ($products as $product) {
            $packageProduct = new PackageProduct(0);
            $packageProduct->setProduct($product);

            for ($quantity = 1; $quantity <= $product->getQuantity(); ++$quantity) {
                // Creating a separate box for products exceeding the maximum parameters
                if (
                    $product->getWeight() > $boxMaxWeight
                    || $product->getHeight() > $boxMaxHeight
                    || $product->getWidth() > $boxMaxWidth
                    || $product->getLength() > $boxMaxLength
                ) {
                    $packageProduct = new PackageProduct(0);
                    $packageProduct->setProduct($product);

                    $separateBox = $boxFactory->createBox();
                    $separateBox->addProduct($packageProduct);

                    $separateBox->changeWeight(
                        $packageProduct
                            ->getProduct()
                            ->getWeight()
                    );

                    $separateBox->changeWidth(
                        $packageProduct
                            ->getProduct()
                            ->getWidth()
                    );

                    $separateBox->changeLength(
                        $packageProduct
                            ->getProduct()
                            ->getLength()
                    );

                    $separateBox->changeHeight(
                        $packageProduct
                            ->getProduct()
                            ->getHeight()
                    );

                    $boxes[] = $separateBox;
                } // Continues to add products to current box
                elseif (
                    $boxCapacity->canAddProduct($product)
                    && $boxDimensions->canAddProduct($product)
                    && $currentBox
                        ->getStrategy()
                        ->canAddProduct($currentBox, $product)
                ) {
                    $boxCapacity->updateCapacity($product);
                    $boxDimensions->updateUsedSide($product);

                    $currentBox->addProduct($packageProduct, $boxCapacity, $boxDimensions);
                } else {
                    // Close the current box and start a new one
                    $currentBox = $boxFactory->createBox();
                    $boxCapacity = new BoxCapacity($boxMaxWeight);
                    $boxDimensions = new BoxDimensions($boxMaxWidth, $boxMaxLength, $boxMaxHeight);

                    $packageProduct = new PackageProduct(0);
                    $packageProduct->setProduct($product);

                    if (
                        $boxCapacity->canAddProduct($product)
                        && $boxDimensions->canAddProduct($product)
                        && $currentBox
                            ->getStrategy()
                            ->canAddProduct($currentBox, $product)
                    ) {
                        $boxCapacity->updateCapacity($product);
                        $boxDimensions->updateUsedSide($product);

                        $currentBox->addProduct($packageProduct, $boxCapacity, $boxDimensions);

                        $boxes[] = $currentBox;
                    }
                }
            }
        }

        return $boxes;
    }
}
