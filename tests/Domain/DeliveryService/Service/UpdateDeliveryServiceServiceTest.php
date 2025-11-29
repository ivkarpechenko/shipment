<?php

namespace App\Tests\Domain\DeliveryService\Service;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\DeliveryService\Service\UpdateDeliveryServiceService;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateDeliveryServiceServiceTest extends KernelTestCase
{
    private DeliveryServiceRepositoryInterface $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->createMock(DeliveryServiceRepositoryInterface::class);
    }

    public function testUpdateNameDeliveryService()
    {
        $service = new UpdateDeliveryServiceService($this->repositoryMock);

        $this->repositoryMock->method('ofCode')->willReturn(DeliveryServiceFixture::getOne(
            'test',
            'test'
        ));

        $service->update('test', 'updated test', null);

        $this->repositoryMock->method('ofCode')->willReturn(DeliveryServiceFixture::getOne(
            'test',
            'updated test'
        ));

        $deliveryService = $this->repositoryMock->ofCode('test');

        $this->assertNotNull($deliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals('test', $deliveryService->getCode());
        $this->assertEquals('updated test', $deliveryService->getName());
        $this->assertTrue($deliveryService->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryService->getId());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNotNull($deliveryService->getUpdatedAt());
    }

    public function testUpdateIsActiveDeliveryService()
    {
        $service = new UpdateDeliveryServiceService($this->repositoryMock);

        $this->repositoryMock->method('ofCode')->willReturn(DeliveryServiceFixture::getOne(
            'test',
            'test'
        ));

        $service->update('test', null, false);

        $this->repositoryMock->method('ofCodeDeactivated')->willReturn(DeliveryServiceFixture::getOneDeactivated(
            'test',
            'test',
            false
        ));

        $deactivatedDeliveryService = $this->repositoryMock->ofCodeDeactivated('test');

        $this->assertNotNull($deactivatedDeliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deactivatedDeliveryService);
        $this->assertEquals('test', $deactivatedDeliveryService->getCode());
        $this->assertEquals('test', $deactivatedDeliveryService->getName());
        $this->assertFalse($deactivatedDeliveryService->isActive());
        $this->assertInstanceOf(Uuid::class, $deactivatedDeliveryService->getId());
        $this->assertNotNull($deactivatedDeliveryService->getCreatedAt());
        $this->assertNotNull($deactivatedDeliveryService->getUpdatedAt());
    }
}
