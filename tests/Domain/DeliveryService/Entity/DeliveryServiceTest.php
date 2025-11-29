<?php

namespace App\Tests\Domain\DeliveryService\Entity;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class DeliveryServiceTest extends KernelTestCase
{
    public function testCreateDeliveryService()
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');

        $this->assertNotNull($deliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals('test', $deliveryService->getCode());
        $this->assertEquals('test', $deliveryService->getName());
        $this->assertTrue($deliveryService->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryService->getId());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNull($deliveryService->getUpdatedAt());
    }

    public function testUpdateDeliveryService()
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');

        $this->assertNotNull($deliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals('test', $deliveryService->getCode());
        $this->assertEquals('test', $deliveryService->getName());
        $this->assertTrue($deliveryService->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryService->getId());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNull($deliveryService->getUpdatedAt());

        $deliveryService->changeName('updated test');

        $this->assertEquals('updated test', $deliveryService->getName());
        $this->assertNotNull($deliveryService->getUpdatedAt());

        $deliveryService->changeIsActive(false);

        $this->assertFalse($deliveryService->isActive());
        $this->assertNotNull($deliveryService->getUpdatedAt());
    }
}
