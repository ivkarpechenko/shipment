<?php

declare(strict_types=1);

namespace App\Tests\Domain\DeliveryMethod\Entity;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class DeliveryMethodTest extends KernelTestCase
{
    public function testCreateDeliveryMethod(): void
    {
        $deliveryMethod = DeliveryMethodFixture::getOne('test', 'test');

        self::assertInstanceOf(DeliveryMethod::class, $deliveryMethod);
        self::assertEquals('test', $deliveryMethod->getName());
        self::assertEquals('test', $deliveryMethod->getCode());
        self::assertTrue($deliveryMethod->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryMethod->getId());
        $this->assertNotNull($deliveryMethod->getCreatedAt());
        $this->assertNull($deliveryMethod->getUpdatedAt());
    }

    public function testActivateDeliveryMethod(): void
    {
        $deliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $deliveryMethod->activate();

        $this->assertNotNull($deliveryMethod);
        self::assertInstanceOf(DeliveryMethod::class, $deliveryMethod);
        self::assertEquals('test', $deliveryMethod->getName());
        self::assertEquals('test', $deliveryMethod->getCode());
        self::assertTrue($deliveryMethod->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryMethod->getId());
        $this->assertNotNull($deliveryMethod->getCreatedAt());
        $this->assertNotNull($deliveryMethod->getUpdatedAt());
    }

    public function testDeactivateDeliveryMethod(): void
    {
        $deliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $deliveryMethod->deactivate();

        $this->assertNotNull($deliveryMethod);
        self::assertInstanceOf(DeliveryMethod::class, $deliveryMethod);
        self::assertEquals('test', $deliveryMethod->getName());
        self::assertEquals('test', $deliveryMethod->getCode());
        self::assertFalse($deliveryMethod->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryMethod->getId());
        $this->assertNotNull($deliveryMethod->getCreatedAt());
        $this->assertNotNull($deliveryMethod->getUpdatedAt());
    }

    public function testChangeName(): void
    {
        $deliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $deliveryMethod->changeName('test update');

        $this->assertNotNull($deliveryMethod);
        self::assertInstanceOf(DeliveryMethod::class, $deliveryMethod);
        self::assertEquals('test update', $deliveryMethod->getName());
        self::assertEquals('test', $deliveryMethod->getCode());
        self::assertTrue($deliveryMethod->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryMethod->getId());
        $this->assertNotNull($deliveryMethod->getCreatedAt());
        $this->assertNotNull($deliveryMethod->getUpdatedAt());
    }

    public function testChangeIsActive(): void
    {
        $deliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $deliveryMethod->changeIsActive(false);

        $this->assertNotNull($deliveryMethod);
        self::assertInstanceOf(DeliveryMethod::class, $deliveryMethod);
        self::assertEquals('test', $deliveryMethod->getName());
        self::assertEquals('test', $deliveryMethod->getCode());
        self::assertFalse($deliveryMethod->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryMethod->getId());
        $this->assertNotNull($deliveryMethod->getCreatedAt());
        $this->assertNotNull($deliveryMethod->getUpdatedAt());
    }
}
