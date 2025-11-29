<?php

namespace App\Tests\Infrastructure\DBAL\Repository\Doctrine\PickupPoint;

use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\PickupPoint\Entity\PickupPoint;
use App\Infrastructure\DBAL\Repository\Doctrine\PickupPoint\DoctrinePickupPointRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\PickupPoint\PickupPointDtoFixture;
use App\Tests\Fixture\PickupPoint\PickupPointFixture;

class DoctrinePickupPointRepositoryTest extends DoctrineTestCase
{
    protected DoctrinePickupPointRepository $doctrinePickupPointRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->doctrinePickupPointRepository = $this->getContainer()->get(DoctrinePickupPointRepository::class);
    }

    public function testCreatePickupPoint()
    {
        $newPickupPoint = $this->getPickupPoint();

        $this->doctrinePickupPointRepository->create($newPickupPoint);

        $pickupPoint = $this->doctrinePickupPointRepository->ofId($newPickupPoint->getId());

        $this->assertNotNull($pickupPoint);
        $this->assertInstanceOf(PickupPoint::class, $pickupPoint);
        $this->assertEquals($newPickupPoint->getId(), $pickupPoint->getId());
        $this->assertEquals($newPickupPoint->getAddress(), $pickupPoint->getAddress());
        $this->assertEquals($newPickupPoint->getCode(), $pickupPoint->getCode());
        $this->assertEquals($newPickupPoint->getDeliveryService()->getId(), $pickupPoint->getDeliveryService()->getId());
        $this->assertEquals($newPickupPoint->getType(), $pickupPoint->getType());
        $this->assertEquals($newPickupPoint->getWeightMax(), $pickupPoint->getWeightMax());
        $this->assertEquals($newPickupPoint->getWeightMin(), $pickupPoint->getWeightMin());
        $this->assertNotNull($pickupPoint->getCreatedAt());
        $this->assertNull($pickupPoint->getUpdatedAt());
    }

    public function testUpdatePickupPoint()
    {
        $newPickupPoint = $this->getPickupPoint();

        $this->doctrinePickupPointRepository->create($newPickupPoint);
        $pickupPoint = $this->doctrinePickupPointRepository->ofId($newPickupPoint->getId());

        $pickupPoint->change(PickupPointDtoFixture::getOne($pickupPoint->getDeliveryService(), code: 'TEST'));

        $this->doctrinePickupPointRepository->update($pickupPoint);
        $pickupPoint = $this->doctrinePickupPointRepository->ofId($newPickupPoint->getId());

        $this->assertNotNull($pickupPoint);
        $this->assertInstanceOf(PickupPoint::class, $pickupPoint);
        $this->assertEquals($newPickupPoint->getId(), $pickupPoint->getId());
        $this->assertEquals($newPickupPoint->getAddress(), $pickupPoint->getAddress());
        $this->assertEquals($newPickupPoint->getCode(), $pickupPoint->getCode());
        $this->assertEquals($newPickupPoint->getDeliveryService()->getId(), $pickupPoint->getDeliveryService()->getId());
        $this->assertEquals($newPickupPoint->getType(), $pickupPoint->getType());
        $this->assertEquals($newPickupPoint->getWeightMax(), $pickupPoint->getWeightMax());
        $this->assertEquals($newPickupPoint->getWeightMin(), $pickupPoint->getWeightMin());
        $this->assertNotNull($pickupPoint->getCreatedAt());
        $this->assertNotNull($pickupPoint->getUpdatedAt());
    }

    public function testOfDeliveryServiceAndCode()
    {
        $newPickupPoint = $this->getPickupPoint();

        $this->doctrinePickupPointRepository->create($newPickupPoint);

        $pickupPoint = $this->doctrinePickupPointRepository->ofDeliveryServiceAndCode($newPickupPoint->getDeliveryService(), $newPickupPoint->getCode());

        $this->assertNotNull($pickupPoint);
        $this->assertInstanceOf(PickupPoint::class, $pickupPoint);
        $this->assertEquals($newPickupPoint->getId(), $pickupPoint->getId());
        $this->assertEquals($newPickupPoint->getAddress(), $pickupPoint->getAddress());
        $this->assertEquals($newPickupPoint->getCode(), $pickupPoint->getCode());
        $this->assertEquals($newPickupPoint->getDeliveryService()->getId(), $pickupPoint->getDeliveryService()->getId());
        $this->assertEquals($newPickupPoint->getType(), $pickupPoint->getType());
        $this->assertEquals($newPickupPoint->getWeightMax(), $pickupPoint->getWeightMax());
        $this->assertEquals($newPickupPoint->getWeightMin(), $pickupPoint->getWeightMin());
        $this->assertNotNull($pickupPoint->getCreatedAt());
        $this->assertNull($pickupPoint->getUpdatedAt());
    }

    protected function getPickupPoint(): PickupPoint
    {
        $container = $this->getContainer();

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRepository = $container->get(DeliveryServiceRepositoryInterface::class);
        $deliveryServiceRepository->create($newDeliveryService);

        return PickupPointFixture::getOne(
            $deliveryServiceRepository->ofId($newDeliveryService->getId())
        );
    }
}
