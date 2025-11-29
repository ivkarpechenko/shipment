<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shipment\Entity;

use App\Domain\Shipment\Entity\CargoRestriction;
use App\Domain\Shipment\Entity\CargoType;
use App\Domain\Shipment\Entity\Shipment;
use App\Tests\Fixture\Shipment\CargoRestrictionFixture;
use App\Tests\Fixture\Shipment\CargoTypeFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class CargoRestrictionTest extends KernelTestCase
{
    public function testCreate(): void
    {
        $cargoRestriction = CargoRestrictionFixture::getOne(
            cargoType: CargoTypeFixture::getOne('code', 'name'),
            shipment: ShipmentFixture::getOneFilled(),
            maxWidth: 100,
            maxHeight: 200,
            maxLength: 300,
            maxWeight: 400,
            maxVolume: 500,
            maxSumDimensions: 600
        );

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
}
