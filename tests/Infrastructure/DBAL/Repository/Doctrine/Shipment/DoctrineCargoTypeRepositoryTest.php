<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\DBAL\Repository\Doctrine\Shipment;

use App\Domain\Shipment\Entity\CargoType;
use App\Infrastructure\DBAL\Repository\Doctrine\Shipment\DoctrineCargoTypeRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\Shipment\CargoTypeFixture;

class DoctrineCargoTypeRepositoryTest extends DoctrineTestCase
{
    private DoctrineCargoTypeRepository $cargoTypeRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cargoTypeRepository = $this->getContainer()->get(DoctrineCargoTypeRepository::class);
    }

    public function testCreate(): void
    {
        $this->assertEmpty($this->cargoTypeRepository->all());

        $newCargoType = CargoTypeFixture::getOne('code', 'name');
        $this->cargoTypeRepository->create($newCargoType);

        $cargoType = $this->cargoTypeRepository->ofId($newCargoType->getId());

        $this->assertNotNull($cargoType);
        $this->assertInstanceOf(CargoType::class, $cargoType);
        $this->assertEquals($newCargoType->getId(), $cargoType->getId());
        $this->assertEquals('code', $cargoType->getCode());
        $this->assertEquals('name', $cargoType->getName());
        $this->assertTrue($cargoType->isActive());
        $this->assertNotNull($cargoType->getCreatedAt());
        $this->assertNull($cargoType->getUpdatedAt());
    }

    public function testUpdate(): void
    {
        $this->assertEmpty($this->cargoTypeRepository->all());

        $newCargoType = CargoTypeFixture::getOne('code', 'name');
        $this->cargoTypeRepository->create($newCargoType);

        $cargoType = $this->cargoTypeRepository->ofId($newCargoType->getId());
        $cargoType->change('updated name', false);

        $this->cargoTypeRepository->update($cargoType);

        $updatedCargoType = $this->cargoTypeRepository->ofId($newCargoType->getId());

        $this->assertNotNull($cargoType);
        $this->assertInstanceOf(CargoType::class, $updatedCargoType);
        $this->assertEquals($cargoType->getId(), $updatedCargoType->getId());
        $this->assertEquals('code', $updatedCargoType->getCode());
        $this->assertEquals('updated name', $updatedCargoType->getName());
        $this->assertFalse($updatedCargoType->isActive());
        $this->assertNotNull($updatedCargoType->getCreatedAt());
        $this->assertNotNull($updatedCargoType->getUpdatedAt());
    }

    public function testOfId(): void
    {
        $this->assertEmpty($this->cargoTypeRepository->all());

        $newCargoType = CargoTypeFixture::getOne('code', 'name');
        $this->cargoTypeRepository->create($newCargoType);

        $cargoType = $this->cargoTypeRepository->ofId($newCargoType->getId());

        $this->assertNotNull($cargoType);
        $this->assertInstanceOf(CargoType::class, $cargoType);
        $this->assertEquals($newCargoType->getId(), $cargoType->getId());
        $this->assertEquals($newCargoType->getCode(), $cargoType->getCode());
        $this->assertEquals($newCargoType->getName(), $cargoType->getName());
        $this->assertTrue($cargoType->isActive());
        $this->assertNotNull($cargoType->getCreatedAt());
        $this->assertNull($cargoType->getUpdatedAt());
    }

    public function testOfCode(): void
    {
        $this->assertEmpty($this->cargoTypeRepository->all());

        $newCargoType = CargoTypeFixture::getOne('code', 'name');
        $this->cargoTypeRepository->create($newCargoType);

        $cargoType = $this->cargoTypeRepository->ofCode($newCargoType->getCode());

        $this->assertNotNull($cargoType);
        $this->assertInstanceOf(CargoType::class, $cargoType);
        $this->assertEquals($newCargoType->getId(), $cargoType->getId());
        $this->assertEquals($newCargoType->getCode(), $cargoType->getCode());
        $this->assertEquals($newCargoType->getName(), $cargoType->getName());
        $this->assertTrue($cargoType->isActive());
        $this->assertNotNull($cargoType->getCreatedAt());
        $this->assertNull($cargoType->getUpdatedAt());
    }

    public function testOfCodeDeactivated(): void
    {
        $this->assertEmpty($this->cargoTypeRepository->all());

        $newCargoType = CargoTypeFixture::getOne('code', 'name');
        $newCargoType->deactivate();

        $this->cargoTypeRepository->create($newCargoType);

        $cargoType = $this->cargoTypeRepository->ofCode($newCargoType->getCode());
        $this->assertNull($cargoType);

        $cargoType = $this->cargoTypeRepository->ofCodeDeactivated($newCargoType->getCode());

        $this->assertNotNull($cargoType);
        $this->assertInstanceOf(CargoType::class, $cargoType);
        $this->assertEquals($newCargoType->getId(), $cargoType->getId());
        $this->assertEquals($newCargoType->getCode(), $cargoType->getCode());
        $this->assertEquals($newCargoType->getName(), $cargoType->getName());
        $this->assertFalse($cargoType->isActive());
        $this->assertNotNull($cargoType->getCreatedAt());
        $this->assertNotNull($cargoType->getUpdatedAt());
    }

    public function testAll(): void
    {
        $this->assertEmpty($this->cargoTypeRepository->all());

        $newCargoType = CargoTypeFixture::getOne('code', 'name');
        $this->cargoTypeRepository->create($newCargoType);

        $cargoTypes = $this->cargoTypeRepository->all();
        $this->assertCount(1, $cargoTypes);

        $cargoType = $cargoTypes[0];

        $this->assertNotNull($cargoType);
        $this->assertInstanceOf(CargoType::class, $cargoType);
        $this->assertEquals($newCargoType->getId(), $cargoType->getId());
        $this->assertEquals('code', $cargoType->getCode());
        $this->assertEquals('name', $cargoType->getName());
        $this->assertTrue($cargoType->isActive());
        $this->assertNotNull($cargoType->getCreatedAt());
        $this->assertNull($cargoType->getUpdatedAt());
    }
}
