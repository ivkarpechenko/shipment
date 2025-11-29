<?php

namespace App\Domain\Shipment\Service;

use App\Domain\Shipment\Entity\Package;
use App\Domain\Shipment\Entity\PackageProduct;
use App\Domain\Shipment\Service\Packing\Enum\CategoryEnum;
use App\Domain\Shipment\Service\Packing\Factory\BoxFactoryInterface;
use App\Domain\Shipment\Service\Packing\Factory\FlammableBoxFactory;
use App\Domain\Shipment\Service\Packing\Factory\FragileBoxFactory;
use App\Domain\Shipment\Service\Packing\Factory\RegularBoxFactory;
use App\Domain\Shipment\Service\Packing\Service\CategorizeProductService;
use App\Domain\Shipment\Service\Packing\Service\GreedyPackerService;

readonly class CreatePackageService
{
    public function __construct(
        public GreedyPackerService $greedyPackerService,
        public CategorizeProductService $categorizeService
    ) {
    }

    public function create(
        array $products,
        // in grams
        float $maxWeight,
        // in millimeters
        int $maxHeight,
        // in millimeters
        int $maxWidth,
        // in millimeters
        int $maxLength
    ): array {
        // Products breakdown by category
        $categories = $this->categorizeService->categorize(
            $products,
            $maxWeight,
            $maxHeight,
            $maxWidth,
            $maxLength
        );

        $packages = [];
        foreach ($categories as $category => $products) {
            // Create box factory by category
            $boxFactory = $this->createBoxFactory(CategoryEnum::from($category));

            // Packaging products using a greedy algorithm
            $boxes = $this->greedyPackerService->pack(
                $boxFactory,
                $products,
                $maxWeight,
                $maxHeight,
                $maxWidth,
                $maxLength
            );

            foreach ($boxes as $box) {
                $packedProducts = $box->getPackedProducts()->toArray();

                if (empty($packedProducts)) {
                    continue;
                }

                $totalPrice = array_sum(
                    array_map(function (PackageProduct $packageProduct) {
                        return $packageProduct->getProduct()->getPrice() * $packageProduct->getQuantity();
                    }, $packedProducts)
                );

                $package = new Package(
                    $totalPrice,
                    $box->getWidth(),
                    $box->getHeight(),
                    $box->getLength(),
                    $box->getWeight()
                );

                foreach ($packedProducts as $packedProduct) {
                    $package->addProduct($packedProduct);
                }

                $packages[] = $package;
            }
        }

        return $packages;
    }

    private function createBoxFactory(CategoryEnum $enum): BoxFactoryInterface
    {
        return match ($enum) {
            CategoryEnum::REGULAR, CategoryEnum::LARGE_OR_HEAVY => new RegularBoxFactory(),
            CategoryEnum::FRAGILE => new FragileBoxFactory(),
            CategoryEnum::FLAMMABLE => new FlammableBoxFactory(),
        };
    }
}
