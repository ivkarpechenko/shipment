<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shipment\Service;

use App\Domain\Shipment\Enum\CargoTypeEnum;
use App\Domain\Shipment\Repository\CargoRestrictionRepositoryInterface;
use App\Domain\Shipment\Service\CheckShipmentService;
use App\Domain\Shipment\Service\Packing\Service\GreedyPackerService;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\Shipment\CargoRestrictionFixture;
use App\Tests\Fixture\Shipment\CargoTypeFixture;
use App\Tests\Fixture\Shipment\PackageFixture;
use App\Tests\Fixture\Shipment\PackageProductFixture;
use App\Tests\Fixture\Shipment\ProductFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\Fixture\Shipment\StoreFixture;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CheckShipmentServiceTest extends KernelTestCase
{
    private CargoRestrictionRepositoryInterface $cargoRestrictionRepositoryMock;

    private LoggerInterface $loggerMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cargoRestrictionRepositoryMock = $this->createMock(CargoRestrictionRepositoryInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
    }

    public function testIsStoresAllowedPickupTrue(): void
    {
        $store = StoreFixture::getOneFilled(
            isPickup: true
        );

        $product = ProductFixture::getOneFilled();
        $product->setStore($store);

        $package = PackageFixture::getOne(10, 20, 30, 40, 50);

        $packageProduct = PackageProductFixture::getOne(1, $product, $package);
        $package->addProduct($packageProduct);

        $shipment = ShipmentFixture::getOneFilled(
            packages: [$package],
        );

        $checkShipmentService = new CheckShipmentService(
            new GreedyPackerService(),
            $this->cargoRestrictionRepositoryMock,
            $this->loggerMock
        );

        self::assertTrue($checkShipmentService->isStoresAllowedPickup($shipment));
    }

    public function testIsStoresAllowedPickupFalse(): void
    {
        $store = StoreFixture::getOneFilled(
            isPickup: false
        );

        $product = ProductFixture::getOneFilled();
        $product->setStore($store);

        $package = PackageFixture::getOne(10, 20, 30, 40, 50);

        $packageProduct = PackageProductFixture::getOne(1, $product, $package);
        $package->addProduct($packageProduct);

        $shipment = ShipmentFixture::getOneFilled(
            packages: [$package],
        );

        $checkShipmentService = new CheckShipmentService(
            new GreedyPackerService(),
            $this->cargoRestrictionRepositoryMock,
            $this->loggerMock
        );

        self::assertFalse($checkShipmentService->isStoresAllowedPickup($shipment));
    }

    public function testIsEqualsRegionTrue(): void
    {
        $regionFiasId = '2b2f0dc9-84c2-4d78-96f5-a977f64eb9e7';
        $shipment = ShipmentFixture::getOneFilled(
            from: AddressFixture::getOneFilled(
                inputData: [
                    'region_fias_id' => $regionFiasId,
                ]
            ),
            to: AddressFixture::getOneFilled(
                inputData: [
                    'region_fias_id' => $regionFiasId,
                ]
            )
        );

        $checkShipmentService = new CheckShipmentService(
            new GreedyPackerService(),
            $this->cargoRestrictionRepositoryMock,
            $this->loggerMock
        );

        $this->assertTrue($checkShipmentService->isEqualRegion($shipment));
    }

    public function testIsEqualRegionFalse(): void
    {
        $shipment = ShipmentFixture::getOneFilled(
            from: AddressFixture::getOneFilled(
                inputData: [
                    'region_fias_id' => '2b2f0dc9-84c2-4d78-96f5-a977f64eb9e7',
                ]
            ),
            to: AddressFixture::getOneFilled(
                inputData: [
                    'region_fias_id' => '7989e8f4-967d-47bc-bfbe-f1ba0b695d84',
                ]
            )
        );

        $checkShipmentService = new CheckShipmentService(
            new GreedyPackerService(),
            $this->cargoRestrictionRepositoryMock,
            $this->loggerMock
        );

        $this->assertFalse($checkShipmentService->isEqualRegion($shipment));
    }

    /**
     * region_fias_id взяты из переменной CheckShipmentService::MSK_REGION_GUIDS
     */
    public function testIsEqualRegionMoscow(): void
    {
        $shipment = ShipmentFixture::getOneFilled(
            from: AddressFixture::getOneFilled(
                inputData: [
                    'region_fias_id' => '0c5b2444-70a0-4932-980c-b4dc0d3f02b5',
                ]
            ),
            to: AddressFixture::getOneFilled(
                inputData: [
                    'region_fias_id' => '29251dcf-00a1-4e34-98d4-5c47484a36d4',
                ]
            )
        );

        $checkShipmentService = new CheckShipmentService(
            new GreedyPackerService(),
            $this->cargoRestrictionRepositoryMock,
            $this->loggerMock
        );

        $this->assertTrue($checkShipmentService->isEqualRegion($shipment));
    }

    /**
     * region_fias_id взяты из переменной CheckShipmentService::SPB_REGION_GUIDS
     */
    public function testIsEqualRegionSpb(): void
    {
        $shipment = ShipmentFixture::getOneFilled(
            from: AddressFixture::getOneFilled(
                inputData: [
                    'region_fias_id' => 'c2deb16a-0330-4f05-821f-1d09c93331e6',
                ]
            ),
            to: AddressFixture::getOneFilled(
                inputData: [
                    'region_fias_id' => '6d1ebb35-70c6-4129-bd55-da3969658f5d',
                ]
            )
        );

        $checkShipmentService = new CheckShipmentService(
            new GreedyPackerService(),
            $this->cargoRestrictionRepositoryMock,
            $this->loggerMock
        );

        $this->assertTrue($checkShipmentService->isEqualRegion($shipment));
    }

    /**
     * region_fias_id взяты из переменной CheckShipmentService::CRIMEA_REGION_GUIDS
     */
    public function testIsEqualRegion(): void
    {
        $shipment = ShipmentFixture::getOneFilled(
            from: AddressFixture::getOneFilled(
                inputData: [
                    'region_fias_id' => '6fdecb78-893a-4e3f-a5ba-aa062459463b',
                ]
            ),
            to: AddressFixture::getOneFilled(
                inputData: [
                    'region_fias_id' => 'bd8e6511-e4b9-4841-90de-6bbc231a789e',
                ]
            )
        );

        $checkShipmentService = new CheckShipmentService(
            new GreedyPackerService(),
            $this->cargoRestrictionRepositoryMock,
            $this->loggerMock
        );

        $this->assertTrue($checkShipmentService->isEqualRegion($shipment));
    }

    public function testGetCargoTypesWithoutCargoRestriction(): void
    {
        $packages[] = PackageFixture::getOne(10, 100, 200, 300, 400);
        $shipment = ShipmentFixture::getOneFilled(packages: $packages);

        $this->cargoRestrictionRepositoryMock->method('ofShipmentId')->willReturn([]);

        $checkShipmentService = new CheckShipmentService(
            new GreedyPackerService(),
            $this->cargoRestrictionRepositoryMock,
            $this->loggerMock
        );

        $cargoTypes = $checkShipmentService->getCargoTypes($shipment);

        $this->assertCount(0, $cargoTypes);
    }

    public function testGetCargoTypesWithCargoRestrictionInclude(): void
    {
        $packages = [
            PackageFixture::getOne(10, 20, 30, 40, 50),
            PackageFixture::getOne(10, 30, 40, 50, 60),
        ];

        $shipment = ShipmentFixture::getOneFilled(packages: $packages);

        $cargoType = CargoTypeFixture::getOne(CargoTypeEnum::SMALL_SIZED->value, 'test');

        $cargoRestriction = CargoRestrictionFixture::getOne(
            cargoType: $cargoType,
            shipment: $shipment,
            maxWidth: 100,
            maxHeight: 200,
            maxLength: 300,
            maxWeight: 600,
            maxVolume: 300000,
            maxSumDimensions: 500
        );

        $this->cargoRestrictionRepositoryMock->method('ofShipmentId')->willReturn([$cargoRestriction]);

        $checkShipmentService = new CheckShipmentService(
            new GreedyPackerService(),
            $this->cargoRestrictionRepositoryMock,
            $this->loggerMock
        );

        $cargoTypes = $checkShipmentService->getCargoTypes($shipment);

        $this->assertCount(1, $cargoTypes);
        $this->assertContains(CargoTypeEnum::SMALL_SIZED, $cargoTypes);
    }

    public function testGetCargoTypesWithCargoRestrictionNotInclude(): void
    {
        $packages = [
            PackageFixture::getOne(10, 20, 30, 40, 50),
            PackageFixture::getOne(10, 30, 40, 50, 60),
        ];

        $shipment = ShipmentFixture::getOneFilled(packages: $packages);

        $cargoType = CargoTypeFixture::getOne('test', 'test');

        $cargoRestriction = CargoRestrictionFixture::getOne(
            cargoType: $cargoType,
            shipment: $shipment,
            maxWidth: 100,
            maxHeight: 200,
            maxLength: 300,
            maxWeight: 600,
            maxVolume: 30000,
            maxSumDimensions: 500
        );

        $this->cargoRestrictionRepositoryMock->method('ofShipmentId')->willReturn([$cargoRestriction]);

        $checkShipmentService = new CheckShipmentService(
            new GreedyPackerService(),
            $this->cargoRestrictionRepositoryMock,
            $this->loggerMock
        );

        $cargoTypes = $checkShipmentService->getCargoTypes($shipment);

        $this->assertCount(1, $cargoTypes);
        $this->assertContains(CargoTypeEnum::LARGE_SIZED, $cargoTypes);
    }
}
