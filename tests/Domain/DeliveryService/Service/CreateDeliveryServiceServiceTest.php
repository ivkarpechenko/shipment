<?php

namespace App\Tests\Domain\DeliveryService\Service;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Exception\DeliveryServiceAlreadyCreatedException;
use App\Domain\DeliveryService\Exception\DeliveryServiceDeactivatedException;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\DeliveryService\Service\CreateDeliveryServiceService;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class CreateDeliveryServiceServiceTest extends KernelTestCase
{
    private DeliveryServiceRepositoryInterface $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->createMock(DeliveryServiceRepositoryInterface::class);
    }

    public function testCreateDeliveryService()
    {
        $service = new CreateDeliveryServiceService($this->repositoryMock);

        $service->create('test', 'test');

        $this->repositoryMock->method('ofCode')->willReturn(DeliveryServiceFixture::getOne(
            'test',
            'test'
        ));

        $deliveryService = $this->repositoryMock->ofCode('test');

        $this->assertNotNull($deliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals('test', $deliveryService->getCode());
        $this->assertEquals('test', $deliveryService->getName());
        $this->assertTrue($deliveryService->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryService->getId());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNull($deliveryService->getUpdatedAt());
    }

    public function testCreateDeliveryServiceIfExists()
    {
        $service = new CreateDeliveryServiceService($this->repositoryMock);

        $this->repositoryMock->method('ofCode')->willReturn(DeliveryServiceFixture::getOne(
            'test',
            'test'
        ));

        $this->expectException(DeliveryServiceAlreadyCreatedException::class);
        $service->create('test', 'test');
    }

    public function testCreateDeliveryServiceIfDeactivated()
    {
        $service = new CreateDeliveryServiceService($this->repositoryMock);

        $this->repositoryMock->method('ofCodeDeactivated')->willReturn(DeliveryServiceFixture::getOneDeactivated(
            'test',
            'test',
            false
        ));

        $this->expectException(DeliveryServiceDeactivatedException::class);
        $service->create('test', 'test');
    }
}
