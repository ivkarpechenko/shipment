<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shipment\Entity;

use App\Domain\Shipment\Entity\CargoType;
use App\Tests\Fixture\Shipment\CargoTypeFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class CargoTypeTest extends KernelTestCase
{
    public function testCreate(): void
    {
        $cargoType = CargoTypeFixture::getOne('code', 'name');

        $this->assertNotNull($cargoType);
        $this->assertInstanceOf(CargoType::class, $cargoType);
        $this->assertEquals('code', $cargoType->getCode());
        $this->assertEquals('name', $cargoType->getName());
        $this->assertTrue($cargoType->isActive());
        $this->assertInstanceOf(Uuid::class, $cargoType->getId());
        $this->assertNotNull($cargoType->getCreatedAt());
        $this->assertNull($cargoType->getUpdatedAt());
    }

    public function testChange(): void
    {
        $cargoType = CargoTypeFixture::getOne('code', 'name');
        $cargoType->change('updated', false);

        $this->assertNotNull($cargoType);
        $this->assertInstanceOf(CargoType::class, $cargoType);
        $this->assertEquals('code', $cargoType->getCode());
        $this->assertEquals('updated', $cargoType->getName());
        $this->assertFalse($cargoType->isActive());
        $this->assertInstanceOf(Uuid::class, $cargoType->getId());
        $this->assertNotNull($cargoType->getCreatedAt());
        $this->assertNotNull($cargoType->getUpdatedAt());
    }

    public function testDeactivate(): void
    {
        $cargoType = CargoTypeFixture::getOne('code', 'name');
        $cargoType->deactivate();

        $this->assertNotNull($cargoType);
        $this->assertInstanceOf(CargoType::class, $cargoType);
        $this->assertEquals('code', $cargoType->getCode());
        $this->assertEquals('name', $cargoType->getName());
        $this->assertFalse($cargoType->isActive());
        $this->assertInstanceOf(Uuid::class, $cargoType->getId());
        $this->assertNotNull($cargoType->getCreatedAt());
        $this->assertNotNull($cargoType->getUpdatedAt());
    }

    public function testActivate(): void
    {
        $cargoType = CargoTypeFixture::getOne('code', 'name');
        $cargoType->activate();

        $this->assertNotNull($cargoType);
        $this->assertInstanceOf(CargoType::class, $cargoType);
        $this->assertEquals('code', $cargoType->getCode());
        $this->assertEquals('name', $cargoType->getName());
        $this->assertTrue($cargoType->isActive());
        $this->assertInstanceOf(Uuid::class, $cargoType->getId());
        $this->assertNotNull($cargoType->getCreatedAt());
        $this->assertNotNull($cargoType->getUpdatedAt());
    }
}
