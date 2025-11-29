<?php

declare(strict_types=1);

namespace App\Tests\Domain\PickupPoint\Entity;

use App\Domain\Address\ValueObject\Point;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\PickupPoint\Entity\PickupPoint;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\PickupPoint\PickupPointDtoFixture;
use App\Tests\Fixture\PickupPoint\PickupPointFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PickupPointTest extends KernelTestCase
{
    public function testCreate(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');

        $pickupPoint = new PickupPoint(
            deliveryService: $deliveryService,
            phones: ['+79999999999'],
            point: new Point(33, 22),
            address: 'test address',
            workTime: 'test workTime',
            name: 'test name',
            code: 'test code',
            type: 'test type',
            weightMin: 0,
            weightMax: 20000,
            width: 20,
            height: 30,
            depth: 40
        );

        $this->assertInstanceOf(PickupPoint::class, $pickupPoint);
        $this->assertInstanceOf(DeliveryService::class, $pickupPoint->getDeliveryService());
        $this->assertEquals('test', $pickupPoint->getDeliveryService()->getCode());
        $this->assertIsArray($pickupPoint->getPhones());
        $this->assertCount(1, $pickupPoint->getPhones());
        $this->assertInstanceOf(Point::class, $pickupPoint->getPoint());
        $this->assertEquals(33, $pickupPoint->getPoint()->getLatitude());
        $this->assertEquals(22, $pickupPoint->getPoint()->getLongitude());
        $this->assertEquals('test address', $pickupPoint->getAddress());
        $this->assertEquals('test workTime', $pickupPoint->getWorkTime());
        $this->assertEquals('test name', $pickupPoint->getName());
        $this->assertEquals('test code', $pickupPoint->getCode());
        $this->assertEquals('test type', $pickupPoint->getType());
        $this->assertEquals(0, $pickupPoint->getWeightMin());
        $this->assertEquals(20000, $pickupPoint->getWeightMax());
        $this->assertEquals(40, $pickupPoint->getDepth());
        $this->assertEquals(20, $pickupPoint->getWidth());
        $this->assertEquals(30, $pickupPoint->getHeight());
        $this->assertNotNull($pickupPoint->getCreatedAt());
        $this->assertNull($pickupPoint->getUpdatedAt());
        $this->assertTrue($pickupPoint->isActive());
    }

    public function testChange(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');

        $pickupPoint = new PickupPoint(
            deliveryService: $deliveryService,
            phones: ['+79999999999'],
            point: new Point(33, 22),
            address: 'test address',
            workTime: 'test workTime',
            name: 'test name',
            code: 'test code',
            type: 'test type',
            weightMin: 0,
            weightMax: 20000,
            width: 20,
            height: 30,
            depth: 40
        );

        $newDeliveryService = DeliveryServiceFixture::getOne('new service', 'new service');

        $pickupPointDto = PickupPointDtoFixture::getOne(
            deliveryService: $newDeliveryService,
            address: 'new address',
            workTime: 'new work time',
            code: 'new code',
            type: 'new type',
            weightMin: 100,
            weightMax: 200,
            latitude: 40,
            longitude: 50,
            width: 1,
            height: 2,
            depth: 3,
            phones: ['+79999999999', '+79999999998'],
            name: 'new name'
        );

        $pickupPoint->change($pickupPointDto);

        $this->assertInstanceOf(PickupPoint::class, $pickupPoint);
        $this->assertInstanceOf(DeliveryService::class, $pickupPoint->getDeliveryService());
        $this->assertEquals('test', $pickupPoint->getDeliveryService()->getCode());
        $this->assertIsArray($pickupPoint->getPhones());
        $this->assertCount(2, $pickupPoint->getPhones());
        $this->assertInstanceOf(Point::class, $pickupPoint->getPoint());
        $this->assertEquals(33, $pickupPoint->getPoint()->getLatitude());
        $this->assertEquals(22, $pickupPoint->getPoint()->getLongitude());
        $this->assertEquals('new address', $pickupPoint->getAddress());
        $this->assertEquals('new work time', $pickupPoint->getWorkTime());
        $this->assertEquals('new name', $pickupPoint->getName());
        $this->assertEquals('test code', $pickupPoint->getCode());
        $this->assertEquals('new type', $pickupPoint->getType());
        $this->assertEquals(100, $pickupPoint->getWeightMin());
        $this->assertEquals(200, $pickupPoint->getWeightMax());
        $this->assertEquals(3, $pickupPoint->getDepth());
        $this->assertEquals(1, $pickupPoint->getWidth());
        $this->assertEquals(2, $pickupPoint->getHeight());
        $this->assertNotNull($pickupPoint->getCreatedAt());
        $this->assertNotNull($pickupPoint->getUpdatedAt());
        $this->assertTrue($pickupPoint->isActive());
    }

    public function testGetWeightMinGram(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $pickupPoint = PickupPointFixture::getOne(
            deliveryService: $deliveryService,
            weightMin: 100
        );

        $this->assertEquals(100000, $pickupPoint->getWeightMinGram());
    }

    public function testGetWeightMinGramIsNull(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $pickupPoint = PickupPointFixture::getOne(
            deliveryService: $deliveryService,
            weightMin: null
        );

        $this->assertNull($pickupPoint->getWeightMinGram());
    }

    public function testGetWeightMaxGram(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $pickupPoint = PickupPointFixture::getOne(
            deliveryService: $deliveryService,
            weightMax: 100
        );

        $this->assertEquals(100000, $pickupPoint->getWeightMaxGram());
    }

    public function testGetWeightMaxGramIsNull(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $pickupPoint = PickupPointFixture::getOne(
            deliveryService: $deliveryService,
            weightMax: null
        );

        $this->assertNull($pickupPoint->getWeightMinGram());
    }

    public function testGetWidthMM(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $pickupPoint = PickupPointFixture::getOne(
            deliveryService: $deliveryService,
            width: 100
        );

        $this->assertEquals(1000, $pickupPoint->getWidthMM());
    }

    public function testGetWidthMMIsNull(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $pickupPoint = PickupPointFixture::getOne(
            deliveryService: $deliveryService,
            width: null
        );

        $this->assertNull($pickupPoint->getWidthMM());
    }

    public function testGetHeightMM(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $pickupPoint = PickupPointFixture::getOne(
            deliveryService: $deliveryService,
            height: 100
        );

        $this->assertEquals(1000, $pickupPoint->getHeightMM());
    }

    public function testGetHeightMMIsNull(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $pickupPoint = PickupPointFixture::getOne(
            deliveryService: $deliveryService,
            height: null
        );

        $this->assertNull($pickupPoint->getHeightMM());
    }

    public function testGetDepthMM(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $pickupPoint = PickupPointFixture::getOne(
            deliveryService: $deliveryService,
            depth: 100
        );

        $this->assertEquals(1000, $pickupPoint->getDepthMM());
    }

    public function testGetDepthMMIsNull(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $pickupPoint = PickupPointFixture::getOne(
            deliveryService: $deliveryService,
            depth: null
        );

        $this->assertNull($pickupPoint->getDepthMM());
    }

    public function testGetMaxDimension(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $pickupPoint = PickupPointFixture::getOne(
            deliveryService: $deliveryService,
            width: 1,
            height: 2,
            depth: 3
        );

        $this->assertEquals(3, $pickupPoint->getMaxDimension());
    }

    public function testChangeIsActive(): void
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $pickupPoint = PickupPointFixture::getOne(
            deliveryService: $deliveryService
        );

        $pickupPoint->changeIsActive(false);

        $this->assertFalse($pickupPoint->isActive());
        $this->assertNotNull($pickupPoint->getUpdatedAt());
    }
}
