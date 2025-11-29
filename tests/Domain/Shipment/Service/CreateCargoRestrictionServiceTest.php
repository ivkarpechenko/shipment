<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shipment\Service;

use App\Domain\Shipment\Entity\CargoRestriction;
use App\Domain\Shipment\Entity\CargoType;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Exception\CargoTypeDeactivatedException;
use App\Domain\Shipment\Exception\CargoTypeNotFoundException;
use App\Domain\Shipment\Exception\ShipmentNotFoundException;
use App\Domain\Shipment\Repository\CargoRestrictionRepositoryInterface;
use App\Domain\Shipment\Repository\CargoTypeRepositoryInterface;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use App\Domain\Shipment\Service\CreateCargoRestrictionService;
use App\Tests\Fixture\Shipment\CargoRestrictionFixture;
use App\Tests\Fixture\Shipment\CargoTypeFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class CreateCargoRestrictionServiceTest extends KernelTestCase
{
    private CargoRestrictionRepositoryInterface $cargoRestrictionRepositoryMock;

    private CargoTypeRepositoryInterface $cargoTypeRepositoryMock;

    private ShipmentRepositoryInterface $shipmentRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cargoRestrictionRepositoryMock = $this->createMock(CargoRestrictionRepositoryInterface::class);
        $this->cargoTypeRepositoryMock = $this->createMock(CargoTypeRepositoryInterface::class);
        $this->shipmentRepositoryMock = $this->createMock(ShipmentRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $cargoType = CargoTypeFixture::getOne('code', 'test');
        $this->cargoTypeRepositoryMock->method('ofCode')->willReturn($cargoType);

        $shipment = ShipmentFixture::getOneFilled();
        $this->shipmentRepositoryMock->method('ofId')->willReturn($shipment);

        $service = new CreateCargoRestrictionService(
            $this->cargoRestrictionRepositoryMock,
            $this->cargoTypeRepositoryMock,
            $this->shipmentRepositoryMock
        );

        $service->create(
            $shipment->getId(),
            $cargoType->getCode(),
            100,
            200,
            300,
            400,
            500,
            600
        );

        $cargoRestriction = CargoRestrictionFixture::getOne(
            cargoType: $cargoType,
            shipment: $shipment,
            maxWidth: 100,
            maxHeight: 200,
            maxLength: 300,
            maxWeight: 400,
            maxVolume: 500,
            maxSumDimensions: 600
        );
        $this->cargoRestrictionRepositoryMock->method('ofId')->willReturn($cargoRestriction);

        $cargoRestriction = $this->cargoRestrictionRepositoryMock->ofId($cargoRestriction->getId());

        $this->assertNotNull($cargoRestriction);
        $this->assertInstanceOf(CargoRestriction::class, $cargoRestriction);
        $this->assertInstanceOf(CargoType::class, $cargoRestriction->getCargoType());
        $this->assertInstanceOf(Shipment::class, $cargoRestriction->getShipment());
        $this->assertEquals(100, $cargoRestriction->getMaxWidth());
        $this->assertEquals(200, $cargoRestriction->getMaxHeight());
        $this->assertEquals(300, $cargoRestriction->getMaxLength());
        $this->assertEquals(400, $cargoRestriction->getMaxWeight());
        $this->assertEquals(500, $cargoRestriction->getMaxVolume());
        $this->assertEquals(600, $cargoRestriction->getMaxSumDimensions());
        $this->assertInstanceOf(Uuid::class, $cargoRestriction->getId());
        $this->assertNotNull($cargoRestriction->getCreatedAt());
        $this->assertNull($cargoRestriction->getUpdatedAt());
    }

    public function testCreateIfShipmentNotFound(): void
    {
        $cargoType = CargoTypeFixture::getOne('code', 'test');
        $this->cargoTypeRepositoryMock->method('ofCode')->willReturn($cargoType);
        $this->shipmentRepositoryMock->method('ofId')->willReturn(null);

        $service = new CreateCargoRestrictionService(
            $this->cargoRestrictionRepositoryMock,
            $this->cargoTypeRepositoryMock,
            $this->shipmentRepositoryMock
        );

        $this->expectException(ShipmentNotFoundException::class);

        $service->create(
            Uuid::v1(),
            $cargoType->getCode(),
            100,
            200,
            300,
            400,
            500,
            600
        );
    }

    public function testCreateIfCargoTypeNotFound(): void
    {
        $this->cargoTypeRepositoryMock->method('ofCode')->willReturn(null);

        $shipment = ShipmentFixture::getOneFilled();
        $this->shipmentRepositoryMock->method('ofId')->willReturn($shipment);

        $service = new CreateCargoRestrictionService(
            $this->cargoRestrictionRepositoryMock,
            $this->cargoTypeRepositoryMock,
            $this->shipmentRepositoryMock
        );

        $this->expectException(CargoTypeNotFoundException::class);
        $service->create(
            $shipment->getId(),
            'test',
            100,
            200,
            300,
            400,
            500,
            600
        );
    }

    public function testCreateIfCargoTypeDeactivated(): void
    {
        $cargoType = CargoTypeFixture::getOne('code', 'name');
        $this->cargoTypeRepositoryMock->method('ofCode')->willReturn(null);
        $this->cargoTypeRepositoryMock->method('ofCodeDeactivated')->willReturn($cargoType);

        $shipment = ShipmentFixture::getOneFilled();
        $this->shipmentRepositoryMock->method('ofId')->willReturn($shipment);

        $service = new CreateCargoRestrictionService(
            $this->cargoRestrictionRepositoryMock,
            $this->cargoTypeRepositoryMock,
            $this->shipmentRepositoryMock
        );

        $this->expectException(CargoTypeDeactivatedException::class);
        $service->create(
            $shipment->getId(),
            $cargoType->getCode(),
            100,
            200,
            300,
            400,
            500,
            600
        );
    }
}
