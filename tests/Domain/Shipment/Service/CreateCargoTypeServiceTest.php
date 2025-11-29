<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shipment\Service;

use App\Domain\Shipment\Exception\CargoTypeAlreadyCreatedException;
use App\Domain\Shipment\Exception\CargoTypeDeactivatedException;
use App\Domain\Shipment\Repository\CargoTypeRepositoryInterface;
use App\Domain\Shipment\Service\CreateCargoTypeService;
use App\Tests\Fixture\Shipment\CargoTypeFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateCargoTypeServiceTest extends KernelTestCase
{
    private CargoTypeRepositoryInterface $cargoTypeRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cargoTypeRepositoryMock = $this->createMock(CargoTypeRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $createCargoTypeService = new CreateCargoTypeService($this->cargoTypeRepositoryMock);

        $createCargoTypeService->create('code', 'test');

        $newCargoType = CargoTypeFixture::getOne('code', 'test');

        $this->cargoTypeRepositoryMock->method('ofId')->willReturn($newCargoType);

        $cargoType = $this->cargoTypeRepositoryMock->ofId($newCargoType->getId());

        $this->assertNotNull($cargoType);
        $this->assertEquals('code', $cargoType->getCode());
        $this->assertEquals('test', $cargoType->getName());
        $this->assertTrue($cargoType->isActive());
        $this->assertNotNull($cargoType->getCreatedAt());
        $this->assertNull($cargoType->getUpdatedAt());
    }

    public function testCreateIfCargoTypeAlreadyExists(): void
    {
        $this->cargoTypeRepositoryMock->method('ofCode')->willReturn(
            CargoTypeFixture::getOne('code', 'name')
        );

        $createCargoTypeService = new CreateCargoTypeService($this->cargoTypeRepositoryMock);

        $this->expectException(CargoTypeAlreadyCreatedException::class);
        $createCargoTypeService->create('code', 'name');
    }

    public function testCreateIfCargoTypeDeactivated(): void
    {
        $this->cargoTypeRepositoryMock->method('ofCode')->willReturn(null);
        $this->cargoTypeRepositoryMock->method('ofCodeDeactivated')->willReturn(
            CargoTypeFixture::getOne('code', 'name')
        );

        $createCargoTypeService = new CreateCargoTypeService($this->cargoTypeRepositoryMock);

        $this->expectException(CargoTypeDeactivatedException::class);
        $createCargoTypeService->create('code', 'name');
    }
}
