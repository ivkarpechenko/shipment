<?php

declare(strict_types=1);

namespace App\Tests\Domain\DeliveryService\Service;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictPackage;
use App\Domain\DeliveryService\Exception\DeliveryServiceDeactivatedException;
use App\Domain\DeliveryService\Exception\DeliveryServiceNotFoundException;
use App\Domain\DeliveryService\Exception\DeliveryServiceRestrictPackageAlreadyCreatedException;
use App\Domain\DeliveryService\Exception\DeliveryServiceRestrictPackageDeactivatedException;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictPackageRepositoryInterface;
use App\Domain\DeliveryService\Service\CreateDeliveryServiceRestrictPackageService;
use App\Tests\Fixture\DeliveryService\DeliverServiceRestrictPackageFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class CreateDeliveryServiceRestrictPackageServiceTest extends KernelTestCase
{
    private DeliveryServiceRepositoryInterface $deliveryServiceRepositoryMock;

    private DeliveryServiceRestrictPackageRepositoryInterface $deliveryServiceRestrictPackageRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deliveryServiceRepositoryMock = $this->createMock(DeliveryServiceRepositoryInterface::class);
        $this->deliveryServiceRestrictPackageRepositoryMock = $this->createMock(DeliveryServiceRestrictPackageRepositoryInterface::class);
    }

    public function testCreate(): void
    {
        $service = new CreateDeliveryServiceRestrictPackageService(
            $this->deliveryServiceRepositoryMock,
            $this->deliveryServiceRestrictPackageRepositoryMock
        );

        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $this->deliveryServiceRepositoryMock->method('ofId')->willReturn($deliveryService);
        $this->deliveryServiceRestrictPackageRepositoryMock->method('ofDeliveryServiceId')->willReturn(null);

        $service->create($deliveryService->getId(), 10, 20, 30, 40);

        $deliveryServiceRestrictPackage = DeliverServiceRestrictPackageFixture::getOne(
            $deliveryService,
            10,
            20,
            30,
            40
        );
        $this->deliveryServiceRestrictPackageRepositoryMock->method('ofId')->willReturn($deliveryServiceRestrictPackage);

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

    public function testCreateIfDeliveryServiceNotFound(): void
    {
        $service = new CreateDeliveryServiceRestrictPackageService(
            $this->deliveryServiceRepositoryMock,
            $this->deliveryServiceRestrictPackageRepositoryMock
        );

        $this->deliveryServiceRepositoryMock->method('ofId')->willReturn(null);

        $this->expectException(DeliveryServiceNotFoundException::class);

        $service->create(Uuid::v1(), 10, 20, 30, 40);
    }

    public function testCreateIfDeliveryServiceDeactivated(): void
    {
        $service = new CreateDeliveryServiceRestrictPackageService(
            $this->deliveryServiceRepositoryMock,
            $this->deliveryServiceRestrictPackageRepositoryMock
        );

        $this->deliveryServiceRepositoryMock->method('ofId')->willReturn(null);
        $this->deliveryServiceRepositoryMock->method('ofIdDeactivated')->willReturn(
            DeliveryServiceFixture::getOneDeactivated('test', 'test', false)
        );

        $this->expectException(DeliveryServiceDeactivatedException::class);

        $service->create(Uuid::v1(), 10, 20, 30, 40);
    }

    public function testCreateIfDeliveryServiceRestrictPackageAlreadyCreated(): void
    {
        $service = new CreateDeliveryServiceRestrictPackageService(
            $this->deliveryServiceRepositoryMock,
            $this->deliveryServiceRestrictPackageRepositoryMock
        );

        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $this->deliveryServiceRepositoryMock->method('ofId')->willReturn($deliveryService);
        $this->deliveryServiceRestrictPackageRepositoryMock->method('ofDeliveryServiceId')->willReturn(
            DeliverServiceRestrictPackageFixture::getOne($deliveryService, 10, 20, 30, 40)
        );

        $this->expectException(DeliveryServiceRestrictPackageAlreadyCreatedException::class);

        $service->create($deliveryService->getId(), 10, 20, 30, 40);
    }

    public function testCreateIfDeliveryServiceRestrictPackageDeactivated(): void
    {
        $service = new CreateDeliveryServiceRestrictPackageService(
            $this->deliveryServiceRepositoryMock,
            $this->deliveryServiceRestrictPackageRepositoryMock
        );

        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $this->deliveryServiceRepositoryMock->method('ofId')->willReturn($deliveryService);
        $this->deliveryServiceRestrictPackageRepositoryMock->method('ofDeliveryServiceIdDeactivated')->willReturn(
            DeliverServiceRestrictPackageFixture::getOne($deliveryService, 10, 20, 30, 40)
        );

        $this->expectException(DeliveryServiceRestrictPackageDeactivatedException::class);

        $service->create($deliveryService->getId(), 10, 20, 30, 40);
    }
}
