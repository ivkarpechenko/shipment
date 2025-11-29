<?php

declare(strict_types=1);

namespace App\Tests\Domain\DeliveryService\Service;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictPackage;
use App\Domain\DeliveryService\Exception\DeliveryServiceRestrictPackageNotFoundException;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictPackageRepositoryInterface;
use App\Domain\DeliveryService\Service\UpdateDeliveryServiceRestrictPackageService;
use App\Tests\Fixture\DeliveryService\DeliverServiceRestrictPackageFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateDeliveryServiceRestrictPackageServiceTest extends KernelTestCase
{
    private DeliveryServiceRestrictPackageRepositoryInterface $deliveryServiceRestrictPackageRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->deliveryServiceRestrictPackageRepositoryMock = $this->createMock(DeliveryServiceRestrictPackageRepositoryInterface::class);
    }

    public function testUpdate(): void
    {
        $service = new UpdateDeliveryServiceRestrictPackageService(
            $this->deliveryServiceRestrictPackageRepositoryMock
        );

        $deliveryServiceRestrictPackage = DeliverServiceRestrictPackageFixture::getOne(
            DeliveryServiceFixture::getOne('test', 'test'),
            10,
            20,
            30,
            40
        );
        $this->deliveryServiceRestrictPackageRepositoryMock->method('ofId')->willReturn($deliveryServiceRestrictPackage);

        $service->update($deliveryServiceRestrictPackage->getId(), 110, 120, 130, 140, true);

        $updatedDeliveryServiceRestrictPackage = $this->deliveryServiceRestrictPackageRepositoryMock->ofId($deliveryServiceRestrictPackage->getId());

        $this->assertNotNull($updatedDeliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryServiceRestrictPackage::class, $updatedDeliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryService::class, $updatedDeliveryServiceRestrictPackage->getDeliveryService());
        $this->assertEquals(110, $updatedDeliveryServiceRestrictPackage->getMaxWeight());
        $this->assertEquals(120, $updatedDeliveryServiceRestrictPackage->getMaxWidth());
        $this->assertEquals(130, $updatedDeliveryServiceRestrictPackage->getMaxHeight());
        $this->assertEquals(140, $updatedDeliveryServiceRestrictPackage->getMaxLength());
        $this->assertTrue($updatedDeliveryServiceRestrictPackage->isActive());
        $this->assertInstanceOf(Uuid::class, $updatedDeliveryServiceRestrictPackage->getId());
        $this->assertNotNull($updatedDeliveryServiceRestrictPackage->getCreatedAt());
        $this->assertNotNull($updatedDeliveryServiceRestrictPackage->getUpdatedAt());
    }

    public function testUpdateIfDeliveryServiceRestrictPackageNotFound(): void
    {
        $service = new UpdateDeliveryServiceRestrictPackageService(
            $this->deliveryServiceRestrictPackageRepositoryMock
        );

        $this->deliveryServiceRestrictPackageRepositoryMock->method('ofId')->willReturn(null);

        $this->expectException(DeliveryServiceRestrictPackageNotFoundException::class);

        $service->update(Uuid::v1(), 110, 120, 130, 140, true);
    }
}
