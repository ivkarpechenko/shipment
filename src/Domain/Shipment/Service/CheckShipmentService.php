<?php

declare(strict_types=1);

namespace App\Domain\Shipment\Service;

use App\Domain\Shipment\Entity\Package;
use App\Domain\Shipment\Entity\PackageProduct;
use App\Domain\Shipment\Entity\Product;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Enum\CargoTypeEnum;
use App\Domain\Shipment\Repository\CargoRestrictionRepositoryInterface;
use App\Domain\Shipment\Service\Packing\Factory\RegularBoxFactory;
use App\Domain\Shipment\Service\Packing\Service\GreedyPackerService;
use Psr\Log\LoggerInterface;

class CheckShipmentService
{
    private const MSK_REGION_GUIDS = [
        '0c5b2444-70a0-4932-980c-b4dc0d3f02b5',
        '29251dcf-00a1-4e34-98d4-5c47484a36d4',
    ];

    private const SPB_REGION_GUIDS = [
        'c2deb16a-0330-4f05-821f-1d09c93331e6',
        '6d1ebb35-70c6-4129-bd55-da3969658f5d',
    ];

    private const CRIMEA_REGION_GUIDS = [
        '6fdecb78-893a-4e3f-a5ba-aa062459463b',
        'bd8e6511-e4b9-4841-90de-6bbc231a789e',
    ];

    public function __construct(
        public GreedyPackerService $greedyPackerService,
        public CargoRestrictionRepositoryInterface $cargoRestrictionRepository,
        public LoggerInterface $logger
    ) {
    }

    public function isStoresAllowedPickup(Shipment $shipment): bool
    {
        $packageIsPickup = $shipment
            ->getPackages()
            ->map(function (Package $package) {
                $storeIsPickup = $package
                    ->getProducts()
                    ->map(function (PackageProduct $packageProduct) {
                        return (bool) $packageProduct->getProduct()?->getStore()?->isPickup();
                    })->toArray();

                // If all stores have is pickup true
                return (bool) array_product($storeIsPickup);
            })
            ->toArray();

        // If all packages have is pickup true
        return (bool) array_product($packageIsPickup);
    }

    public function isEqualRegion(Shipment $shipment): bool
    {
        $fromRegionFiatId = $shipment
            ->getFrom()
            ->getInputDataBy('region_fias_id')
            ?->current();

        $toRegionFiatId = $shipment
            ->getTo()
            ->getInputDataBy('region_fias_id')
            ?->current();

        if (empty($fromRegionFiatId) || empty($toRegionFiatId)) {
            return false;
        }

        return $fromRegionFiatId == $toRegionFiatId
            || (
                in_array($fromRegionFiatId, self::MSK_REGION_GUIDS)
                && in_array($toRegionFiatId, self::MSK_REGION_GUIDS)
            )
            || (
                in_array($fromRegionFiatId, self::SPB_REGION_GUIDS)
                && in_array($toRegionFiatId, self::SPB_REGION_GUIDS)
            )
            || (
                in_array($fromRegionFiatId, self::CRIMEA_REGION_GUIDS)
                && in_array($toRegionFiatId, self::CRIMEA_REGION_GUIDS)
            );
    }

    public function shipmentInMsk(Shipment $shipment): bool
    {
        $fromRegionFiatId = $shipment
            ->getFrom()
            ->getInputDataBy('region_fias_id')
            ?->current();

        $toRegionFiatId = $shipment
            ->getTo()
            ->getInputDataBy('region_fias_id')
            ?->current();

        if (empty($fromRegionFiatId) || empty($toRegionFiatId)) {
            return false;
        }

        return in_array($fromRegionFiatId, self::MSK_REGION_GUIDS) && in_array($toRegionFiatId, self::MSK_REGION_GUIDS);
    }

    /**
     * Вычисление типа груза отправления
     */
    public function getCargoTypes(Shipment $shipment): array
    {
        /**
         * TODO приводим упаковку к сущности товара. По сути для расчета размеров коробки это не важно,
         *  но лучше сделать отдельный упаковщик для упаковок или расширить существующий
         */
        $products = [];

        foreach ($shipment->getPackages() as $package) {
            $products[] = new Product(
                code: 'package',
                description: 'package',
                price: '0',
                weight: $package->getWeight(),
                width: $package->getWidth(),
                height: $package->getHeight(),
                length: $package->getLength(),
                quantity: 1
            );
        }

        $boxFactory = new RegularBoxFactory();
        // получаем все ограничения по грузу для данного отправления
        $cargoRestrictions = $this->cargoRestrictionRepository->ofShipmentId($shipment->getId());

        // если нет ограничений, считаем что груз подходит под все типы
        if (empty($cargoRestrictions)) {
            return [];
        }

        foreach ($cargoRestrictions as $cargoRestriction) {
            // формируем коробки для упаковок, в качестве максимальных размеров коробки берем данные из ограничений
            $boxes = $this->greedyPackerService->pack(
                boxFactory: $boxFactory,
                products: $products,
                boxMaxWeight: $cargoRestriction->getMaxWeight(),
                boxMaxHeight: $cargoRestriction->getMaxHeight(),
                boxMaxWidth: $cargoRestriction->getMaxWidth(),
                boxMaxLength: $cargoRestriction->getMaxLength()
            );

            // если коробок получилось больше одной, значит груз попадает под ограничение
            if (count($boxes) > 1) {
                continue;
            }

            // проверяем по максимальным параметрам. Если проходит, то груз не попадает под ограничение
            $box = current($boxes);
            if (
                $box->getWidth() <= $cargoRestriction->getMaxWidth()
                && $box->getHeight() <= $cargoRestriction->getMaxHeight()
                && $box->getLength() <= $cargoRestriction->getMaxLength()
                && $box->getVolume() <= $cargoRestriction->getMaxVolume()
                && $box->getSumDimensions() <= $cargoRestriction->getMaxSumDimensions()
            ) {
                try {
                    $cargoTypes[] = CargoTypeEnum::from($cargoRestriction->getCargoType()->getCode());
                } catch (\ValueError $e) {
                    $this->logger->error(sprintf("Cargo type %s doesn't support", $cargoRestriction->getCargoType()->getCode()));

                    continue;
                }
            }
        }

        // если груз не попадет в ограничения - считаем его крупногабаритным
        if (empty($cargoTypes)) {
            $cargoTypes = [CargoTypeEnum::LARGE_SIZED];
        }

        return $cargoTypes;
    }
}
