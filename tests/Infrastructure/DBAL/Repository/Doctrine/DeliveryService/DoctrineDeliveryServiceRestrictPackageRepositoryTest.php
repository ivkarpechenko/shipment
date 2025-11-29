<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\DBAL\Repository\Doctrine\DeliveryService;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictPackage;
use App\Infrastructure\DBAL\Repository\Doctrine\DeliveryService\DoctrineDeliveryServiceRepository;
use App\Infrastructure\DBAL\Repository\Doctrine\DeliveryService\DoctrineDeliveryServiceRestrictPackageRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\DeliveryService\DeliverServiceRestrictPackageFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use Symfony\Component\Uid\Uuid;

class DoctrineDeliveryServiceRestrictPackageRepositoryTest extends DoctrineTestCase
{
    private DoctrineDeliveryServiceRestrictPackageRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = static::getContainer()->get(DoctrineDeliveryServiceRestrictPackageRepository::class);
    }

    public function testCreate(): void
    {
        $this->assertEmpty($this->repository->all());

        $container = static::getContainer();
        $deliveryServiceRepository = $container->get(DoctrineDeliveryServiceRepository::class);
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRepository->create($newDeliveryService);
        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());

        $newDeliveryServiceRestrictPackage = DeliverServiceRestrictPackageFixture::getOne(
            deliveryService: $deliveryService,
            maxWeight: 10,
            maxWidth: 20,
            maxHeight: 30,
            maxLength: 40
        );
        $this->repository->create($newDeliveryServiceRestrictPackage);
        $deliveryServiceRestrictPackage = $this->repository->ofId($newDeliveryServiceRestrictPackage->getId());

        $this->assertNotNull($deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryServiceRestrictPackage::class, $deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryService::class, $deliveryServiceRestrictPackage->getDeliveryService());
        $this->assertEquals(10, $deliveryServiceRestrictPackage->getMaxWeight());
        $this->assertEquals(20, $deliveryServiceRestrictPackage->getMaxWidth());
        $this->assertEquals(30, $deliveryServiceRestrictPackage->getMaxHeight());
        $this->assertEquals(40, $deliveryServiceRestrictPackage->getMaxLength());
        $this->assertTrue($deliveryServiceRestrictPackage->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryServiceRestrictPackage->getId());
        $this->assertNotNull($deliveryServiceRestrictPackage->getCreatedAt());
        $this->assertNull($deliveryServiceRestrictPackage->getUpdatedAt());
    }

    public function testUpdate(): void
    {
        $this->assertEmpty($this->repository->all());

        $container = static::getContainer();
        $deliveryServiceRepository = $container->get(DoctrineDeliveryServiceRepository::class);
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRepository->create($newDeliveryService);
        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());

        $newDeliveryServiceRestrictPackage = DeliverServiceRestrictPackageFixture::getOne(
            deliveryService: $deliveryService,
            maxWeight: 10,
            maxWidth: 20,
            maxHeight: 30,
            maxLength: 40
        );
        $this->repository->create($newDeliveryServiceRestrictPackage);
        $deliveryServiceRestrictPackage = $this->repository->ofId($newDeliveryServiceRestrictPackage->getId());

        $this->assertNotNull($deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryServiceRestrictPackage::class, $deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryService::class, $deliveryServiceRestrictPackage->getDeliveryService());
        $this->assertEquals(10, $deliveryServiceRestrictPackage->getMaxWeight());
        $this->assertEquals(20, $deliveryServiceRestrictPackage->getMaxWidth());
        $this->assertEquals(30, $deliveryServiceRestrictPackage->getMaxHeight());
        $this->assertEquals(40, $deliveryServiceRestrictPackage->getMaxLength());
        $this->assertTrue($deliveryServiceRestrictPackage->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryServiceRestrictPackage->getId());
        $this->assertNotNull($deliveryServiceRestrictPackage->getCreatedAt());
        $this->assertNull($deliveryServiceRestrictPackage->getUpdatedAt());

        $deliveryServiceRestrictPackage->change(110, 120, 130, 140, true);
        $this->repository->update($deliveryServiceRestrictPackage);
        $deliveryServiceRestrictPackage = $this->repository->ofId($deliveryServiceRestrictPackage->getId());

        $this->assertNotNull($deliveryServiceRestrictPackage);
        $this->assertEquals($newDeliveryServiceRestrictPackage->getId(), $deliveryServiceRestrictPackage->getId());
        $this->assertInstanceOf(DeliveryServiceRestrictPackage::class, $deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryService::class, $deliveryServiceRestrictPackage->getDeliveryService());
        $this->assertEquals(110, $deliveryServiceRestrictPackage->getMaxWeight());
        $this->assertEquals(120, $deliveryServiceRestrictPackage->getMaxWidth());
        $this->assertEquals(130, $deliveryServiceRestrictPackage->getMaxHeight());
        $this->assertEquals(140, $deliveryServiceRestrictPackage->getMaxLength());
        $this->assertTrue($deliveryServiceRestrictPackage->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryServiceRestrictPackage->getId());
        $this->assertNotNull($deliveryServiceRestrictPackage->getCreatedAt());
        $this->assertNotNull($deliveryServiceRestrictPackage->getUpdatedAt());
    }

    public function testOfId(): void
    {
        $this->assertEmpty($this->repository->all());

        $container = static::getContainer();
        $deliveryServiceRepository = $container->get(DoctrineDeliveryServiceRepository::class);
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRepository->create($newDeliveryService);
        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());

        $newDeliveryServiceRestrictPackage = DeliverServiceRestrictPackageFixture::getOne(
            deliveryService: $deliveryService,
            maxWeight: 10,
            maxWidth: 20,
            maxHeight: 30,
            maxLength: 40
        );
        $this->repository->create($newDeliveryServiceRestrictPackage);
        $deliveryServiceRestrictPackage = $this->repository->ofId($newDeliveryServiceRestrictPackage->getId());

        $this->assertNotNull($deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryServiceRestrictPackage::class, $deliveryServiceRestrictPackage);
        $this->assertEquals($newDeliveryServiceRestrictPackage->getId(), $deliveryServiceRestrictPackage->getId());
        $this->assertInstanceOf(DeliveryService::class, $deliveryServiceRestrictPackage->getDeliveryService());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getDeliveryService()->getId(), $deliveryServiceRestrictPackage->getDeliveryService()->getId());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxWeight(), $deliveryServiceRestrictPackage->getMaxWeight());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxWidth(), $deliveryServiceRestrictPackage->getMaxWidth());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxHeight(), $deliveryServiceRestrictPackage->getMaxHeight());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxLength(), $deliveryServiceRestrictPackage->getMaxLength());
        $this->assertEquals($newDeliveryServiceRestrictPackage->isActive(), $deliveryServiceRestrictPackage->isActive());
    }

    public function testOfIdDeactivated(): void
    {
        $this->assertEmpty($this->repository->all());

        $container = static::getContainer();
        $deliveryServiceRepository = $container->get(DoctrineDeliveryServiceRepository::class);
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRepository->create($newDeliveryService);
        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());

        $newDeliveryServiceRestrictPackage = DeliverServiceRestrictPackageFixture::getOne(
            deliveryService: $deliveryService,
            maxWeight: 10,
            maxWidth: 20,
            maxHeight: 30,
            maxLength: 40
        );
        $newDeliveryServiceRestrictPackage->deactivate();
        $this->repository->create($newDeliveryServiceRestrictPackage);
        $deliveryServiceRestrictPackage = $this->repository->ofId($newDeliveryServiceRestrictPackage->getId());

        $this->assertNull($deliveryServiceRestrictPackage);

        $deliveryServiceRestrictPackage = $this->repository->ofIdDeactivated($newDeliveryServiceRestrictPackage->getId());

        $this->assertNotNull($deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryServiceRestrictPackage::class, $deliveryServiceRestrictPackage);
        $this->assertEquals($newDeliveryServiceRestrictPackage->getId(), $deliveryServiceRestrictPackage->getId());
        $this->assertInstanceOf(DeliveryService::class, $deliveryServiceRestrictPackage->getDeliveryService());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getDeliveryService()->getId(), $deliveryServiceRestrictPackage->getDeliveryService()->getId());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxWeight(), $deliveryServiceRestrictPackage->getMaxWeight());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxWidth(), $deliveryServiceRestrictPackage->getMaxWidth());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxHeight(), $deliveryServiceRestrictPackage->getMaxHeight());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxLength(), $deliveryServiceRestrictPackage->getMaxLength());
        $this->assertEquals($newDeliveryServiceRestrictPackage->isActive(), $deliveryServiceRestrictPackage->isActive());
    }

    public function testOfDeliveryServiceId(): void
    {
        $this->assertEmpty($this->repository->all());

        $container = static::getContainer();
        $deliveryServiceRepository = $container->get(DoctrineDeliveryServiceRepository::class);
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRepository->create($newDeliveryService);
        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());

        $newDeliveryServiceRestrictPackage = DeliverServiceRestrictPackageFixture::getOne(
            deliveryService: $deliveryService,
            maxWeight: 10,
            maxWidth: 20,
            maxHeight: 30,
            maxLength: 40
        );
        $this->repository->create($newDeliveryServiceRestrictPackage);

        $deliveryServiceRestrictPackage = $this->repository->ofDeliveryServiceId($newDeliveryServiceRestrictPackage->getDeliveryService()->getId());

        $this->assertNotNull($deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryServiceRestrictPackage::class, $deliveryServiceRestrictPackage);
        $this->assertEquals($newDeliveryServiceRestrictPackage->getId(), $deliveryServiceRestrictPackage->getId());
        $this->assertInstanceOf(DeliveryService::class, $deliveryServiceRestrictPackage->getDeliveryService());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getDeliveryService()->getId(), $deliveryServiceRestrictPackage->getDeliveryService()->getId());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxWeight(), $deliveryServiceRestrictPackage->getMaxWeight());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxWidth(), $deliveryServiceRestrictPackage->getMaxWidth());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxHeight(), $deliveryServiceRestrictPackage->getMaxHeight());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxLength(), $deliveryServiceRestrictPackage->getMaxLength());
        $this->assertEquals($newDeliveryServiceRestrictPackage->isActive(), $deliveryServiceRestrictPackage->isActive());
    }

    public function testofDeliveryServiceIdDeactivated(): void
    {
        $this->assertEmpty($this->repository->all());

        $container = static::getContainer();
        $deliveryServiceRepository = $container->get(DoctrineDeliveryServiceRepository::class);
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRepository->create($newDeliveryService);
        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());

        $newDeliveryServiceRestrictPackage = DeliverServiceRestrictPackageFixture::getOne(
            deliveryService: $deliveryService,
            maxWeight: 10,
            maxWidth: 20,
            maxHeight: 30,
            maxLength: 40
        );
        $newDeliveryServiceRestrictPackage->deactivate();
        $this->repository->create($newDeliveryServiceRestrictPackage);

        $deliveryServiceRestrictPackage = $this->repository->ofDeliveryServiceId($newDeliveryServiceRestrictPackage->getDeliveryService()->getId());

        $this->assertNull($deliveryServiceRestrictPackage);

        $deliveryServiceRestrictPackage = $this->repository->ofDeliveryServiceIdDeactivated($newDeliveryServiceRestrictPackage->getDeliveryService()->getId());

        $this->assertNotNull($deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryServiceRestrictPackage::class, $deliveryServiceRestrictPackage);
        $this->assertEquals($newDeliveryServiceRestrictPackage->getId(), $deliveryServiceRestrictPackage->getId());
        $this->assertInstanceOf(DeliveryService::class, $deliveryServiceRestrictPackage->getDeliveryService());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getDeliveryService()->getId(), $deliveryServiceRestrictPackage->getDeliveryService()->getId());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxWeight(), $deliveryServiceRestrictPackage->getMaxWeight());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxWidth(), $deliveryServiceRestrictPackage->getMaxWidth());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxHeight(), $deliveryServiceRestrictPackage->getMaxHeight());
        $this->assertEquals($newDeliveryServiceRestrictPackage->getMaxLength(), $deliveryServiceRestrictPackage->getMaxLength());
        $this->assertEquals($newDeliveryServiceRestrictPackage->isActive(), $deliveryServiceRestrictPackage->isActive());
    }
}
