<?php

declare(strict_types=1);

namespace App\Tests\Domain\DeliveryService\Entity;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictPackage;
use App\Tests\Fixture\DeliveryService\DeliverServiceRestrictPackageFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class DeliveryServiceRestrictPackageTest extends KernelTestCase
{
    public function testCreate(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRestrictPackage = DeliverServiceRestrictPackageFixture::getOne(
            deliveryService: $deliveryService,
            maxWeight: 100,
            maxWidth: 100,
            maxHeight: 100,
            maxLength: 100
        );

        $this->assertNotNull($deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryServiceRestrictPackage::class, $deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryService::class, $deliveryServiceRestrictPackage->getDeliveryService());
        $this->assertEquals(100, $deliveryServiceRestrictPackage->getMaxWeight());
        $this->assertEquals(100, $deliveryServiceRestrictPackage->getMaxWidth());
        $this->assertEquals(100, $deliveryServiceRestrictPackage->getMaxHeight());
        $this->assertEquals(100, $deliveryServiceRestrictPackage->getMaxLength());
        $this->assertTrue($deliveryServiceRestrictPackage->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryServiceRestrictPackage->getId());
        $this->assertNotNull($deliveryServiceRestrictPackage->getCreatedAt());
        $this->assertNull($deliveryServiceRestrictPackage->getUpdatedAt());
    }

    public function testActivate(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRestrictPackage = DeliverServiceRestrictPackageFixture::getOne(
            deliveryService: $deliveryService,
            maxWeight: 100,
            maxWidth: 100,
            maxHeight: 100,
            maxLength: 100
        );
        $deliveryServiceRestrictPackage->activate();

        $this->assertNotNull($deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryServiceRestrictPackage::class, $deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryService::class, $deliveryServiceRestrictPackage->getDeliveryService());
        $this->assertEquals(100, $deliveryServiceRestrictPackage->getMaxWeight());
        $this->assertEquals(100, $deliveryServiceRestrictPackage->getMaxWidth());
        $this->assertEquals(100, $deliveryServiceRestrictPackage->getMaxHeight());
        $this->assertEquals(100, $deliveryServiceRestrictPackage->getMaxLength());
        $this->assertTrue($deliveryServiceRestrictPackage->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryServiceRestrictPackage->getId());
        $this->assertNotNull($deliveryServiceRestrictPackage->getCreatedAt());
        $this->assertNotNull($deliveryServiceRestrictPackage->getUpdatedAt());
    }

    public function testDeactivate(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRestrictPackage = DeliverServiceRestrictPackageFixture::getOne(
            deliveryService: $deliveryService,
            maxWeight: 10,
            maxWidth: 20,
            maxHeight: 30,
            maxLength: 40
        );
        $deliveryServiceRestrictPackage->deactivate();

        $this->assertNotNull($deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryServiceRestrictPackage::class, $deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryService::class, $deliveryServiceRestrictPackage->getDeliveryService());
        $this->assertEquals(10, $deliveryServiceRestrictPackage->getMaxWeight());
        $this->assertEquals(20, $deliveryServiceRestrictPackage->getMaxWidth());
        $this->assertEquals(30, $deliveryServiceRestrictPackage->getMaxHeight());
        $this->assertEquals(40, $deliveryServiceRestrictPackage->getMaxLength());
        $this->assertFalse($deliveryServiceRestrictPackage->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryServiceRestrictPackage->getId());
        $this->assertNotNull($deliveryServiceRestrictPackage->getCreatedAt());
        $this->assertNotNull($deliveryServiceRestrictPackage->getUpdatedAt());
    }

    public function testChange(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRestrictPackage = DeliverServiceRestrictPackageFixture::getOne(
            deliveryService: $deliveryService,
            maxWeight: 10,
            maxWidth: 20,
            maxHeight: 30,
            maxLength: 40
        );

        $deliveryServiceRestrictPackage->change(110, 120, 130, 140, false);
        $this->assertNotNull($deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryServiceRestrictPackage::class, $deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryService::class, $deliveryServiceRestrictPackage->getDeliveryService());
        $this->assertEquals(110, $deliveryServiceRestrictPackage->getMaxWeight());
        $this->assertEquals(120, $deliveryServiceRestrictPackage->getMaxWidth());
        $this->assertEquals(130, $deliveryServiceRestrictPackage->getMaxHeight());
        $this->assertEquals(140, $deliveryServiceRestrictPackage->getMaxLength());
        $this->assertFalse($deliveryServiceRestrictPackage->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryServiceRestrictPackage->getId());
        $this->assertNotNull($deliveryServiceRestrictPackage->getCreatedAt());
        $this->assertNotNull($deliveryServiceRestrictPackage->getUpdatedAt());
    }
}
