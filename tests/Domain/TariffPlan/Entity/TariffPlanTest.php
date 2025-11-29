<?php

namespace App\Tests\Domain\TariffPlan\Entity;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class TariffPlanTest extends KernelTestCase
{
    public function testCreateTariffPlan()
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryMethod = DeliveryMethodFixture::getOne('test', 'test');

        $tariffPlan = TariffPlanFixture::getOne($deliveryService, $deliveryMethod, 'test', 'test');

        $this->assertNotNull($tariffPlan);
        $this->assertInstanceOf(TariffPlan::class, $tariffPlan);
        $this->assertEquals('test', $tariffPlan->getCode());
        $this->assertEquals('test', $tariffPlan->getName());
        $this->assertInstanceOf(DeliveryService::class, $tariffPlan->getDeliveryService());
        $this->assertEquals($deliveryService, $tariffPlan->getDeliveryService());
        $this->assertTrue($tariffPlan->isActive());
        $this->assertInstanceOf(Uuid::class, $tariffPlan->getId());
        $this->assertNotNull($tariffPlan->getCreatedAt());
        $this->assertNull($tariffPlan->getUpdatedAt());
    }

    public function testUpdateTariffPlan()
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $tariffPlan = TariffPlanFixture::getOne($deliveryService, $deliveryMethod, 'test', 'test');

        $this->assertNotNull($tariffPlan);
        $this->assertInstanceOf(TariffPlan::class, $tariffPlan);
        $this->assertEquals('test', $tariffPlan->getCode());
        $this->assertEquals('test', $tariffPlan->getName());
        $this->assertInstanceOf(DeliveryService::class, $tariffPlan->getDeliveryService());
        $this->assertEquals($deliveryService, $tariffPlan->getDeliveryService());
        $this->assertTrue($tariffPlan->isActive());
        $this->assertInstanceOf(Uuid::class, $tariffPlan->getId());
        $this->assertNotNull($tariffPlan->getCreatedAt());
        $this->assertNull($tariffPlan->getUpdatedAt());

        $tariffPlan->changeName('updated test');

        $this->assertEquals('updated test', $tariffPlan->getName());
        $this->assertNotNull($tariffPlan->getUpdatedAt());

        $tariffPlan->changeIsActive(false);

        $this->assertFalse($tariffPlan->isActive());
        $this->assertNotNull($tariffPlan->getUpdatedAt());
    }
}
