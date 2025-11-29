<?php

namespace App\Tests\Infrastructure\DBAL\Repository\Doctrine\DeliveryService;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Infrastructure\DBAL\Repository\Doctrine\DeliveryService\DoctrineDeliveryServiceRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;

class DoctrineDeliveryServiceRepositoryTest extends DoctrineTestCase
{
    private DoctrineDeliveryServiceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->getContainer()->get(DoctrineDeliveryServiceRepository::class);
    }

    public function testCreateDeliveryService()
    {
        $this->assertEmpty($this->repository->all());

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');

        $this->repository->create($newDeliveryService);

        $deliveryService = $this->repository->ofId($newDeliveryService->getId());

        $this->assertNotNull($deliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals($newDeliveryService->getId(), $deliveryService->getId());
        $this->assertEquals($newDeliveryService->getCode(), $deliveryService->getCode());
        $this->assertEquals($newDeliveryService->getName(), $deliveryService->getName());
    }

    public function testUpdateDeliveryService()
    {
        $this->assertEmpty($this->repository->all());

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');

        $this->repository->create($newDeliveryService);

        $this->assertEquals('test', $newDeliveryService->getCode());

        $deliveryService = $this->repository->ofId($newDeliveryService->getId());

        $deliveryService->changeName('updated test');
        $this->repository->update($deliveryService);

        $updatedDeliveryService = $this->repository->ofId($newDeliveryService->getId());

        $this->assertNotNull($updatedDeliveryService->getUpdatedAt());
        $this->assertEquals('updated test', $updatedDeliveryService->getName());

        $this->assertTrue($updatedDeliveryService->isActive());

        $updatedDeliveryService->changeIsActive(false);
        $this->repository->update($updatedDeliveryService);

        $deactivatedDeliveryService = $this->repository->ofIdDeactivated($newDeliveryService->getId());

        $this->assertNotNull($deactivatedDeliveryService);
        $this->assertFalse($deactivatedDeliveryService->isActive());
    }

    public function testOfId()
    {
        $this->assertEmpty($this->repository->all());

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $this->repository->create($newDeliveryService);

        $deliveryService = $this->repository->ofId($newDeliveryService->getId());

        $this->assertNotNull($deliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals($newDeliveryService->getId(), $deliveryService->getId());
        $this->assertEquals($newDeliveryService->getCode(), $deliveryService->getCode());
        $this->assertEquals($newDeliveryService->getName(), $deliveryService->getName());
        $this->assertTrue($deliveryService->isActive());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNull($deliveryService->getUpdatedAt());
    }

    public function testOfCode()
    {
        $this->assertEmpty($this->repository->all());

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $this->repository->create($newDeliveryService);

        $deliveryService = $this->repository->ofCode($newDeliveryService->getCode());

        $this->assertNotNull($deliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals($newDeliveryService->getId(), $deliveryService->getId());
        $this->assertEquals($newDeliveryService->getCode(), $deliveryService->getCode());
        $this->assertEquals($newDeliveryService->getName(), $deliveryService->getName());
        $this->assertTrue($deliveryService->isActive());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNull($deliveryService->getUpdatedAt());
    }

    public function testOfIdDeactivated()
    {
        $this->assertEmpty($this->repository->all());

        $newDeliveryService = DeliveryServiceFixture::getOneDeactivated('test', 'test', false);
        $this->repository->create($newDeliveryService);

        $deliveryService = $this->repository->ofIdDeactivated($newDeliveryService->getId());

        $this->assertNotNull($deliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals($newDeliveryService->getId(), $deliveryService->getId());
        $this->assertEquals($newDeliveryService->getCode(), $deliveryService->getCode());
        $this->assertEquals($newDeliveryService->getName(), $deliveryService->getName());
        $this->assertFalse($deliveryService->isActive());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNotNull($deliveryService->getUpdatedAt());
    }

    public function testOfCodeDeactivated()
    {
        $this->assertEmpty($this->repository->all());

        $newDeliveryService = DeliveryServiceFixture::getOneDeactivated('test', 'test', false);
        $this->repository->create($newDeliveryService);

        $deliveryService = $this->repository->ofCodeDeactivated($newDeliveryService->getCode());

        $this->assertNotNull($deliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals($newDeliveryService->getId(), $deliveryService->getId());
        $this->assertEquals($newDeliveryService->getCode(), $deliveryService->getCode());
        $this->assertEquals($newDeliveryService->getName(), $deliveryService->getName());
        $this->assertFalse($deliveryService->isActive());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNotNull($deliveryService->getUpdatedAt());
    }

    public function testAll(): void
    {
        $this->assertEmpty($this->repository->all());

        $firstDeliveryService = DeliveryServiceFixture::getOne('test1', 'test1');
        $this->repository->create($firstDeliveryService);
        $secondDeliveryService = DeliveryServiceFixture::getOne('test2', 'test2');
        $this->repository->create($secondDeliveryService);
        $deliveryServices = $this->repository->all();

        $this->assertCount(2, $deliveryServices);

        $deliveryService = $this->repository->ofId($firstDeliveryService->getId());
        $deliveryService->changeIsActive(false);
        $this->repository->update($deliveryService);
        $activeDeliveryServices = $this->repository->all(true);

        $this->assertCount(1, $activeDeliveryServices);

        $deactivatedDeliveryServices = $this->repository->all(false);

        $this->assertCount(1, $deactivatedDeliveryServices);
    }
}
