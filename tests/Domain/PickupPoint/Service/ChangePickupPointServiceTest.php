<?php

namespace App\Tests\Domain\PickupPoint\Service;

use App\Domain\PickupPoint\Entity\PickupPoint;
use App\Domain\PickupPoint\Service\ChangePickupPointService;
use App\Domain\PickupPoint\Service\CreatePickupPointService;
use App\Infrastructure\DBAL\Repository\Doctrine\PickupPoint\DoctrinePickupPointRepository;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\PickupPoint\PickupPointDtoFixture;
use App\Tests\Fixture\PickupPoint\PickupPointFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChangePickupPointServiceTest extends KernelTestCase
{
    protected DoctrinePickupPointRepository $doctrinePickupPointRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->doctrinePickupPointRepository = $this->createMock(DoctrinePickupPointRepository::class);
    }

    public function testChange()
    {
        $address = AddressFixture::getOneFilled();
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');

        $newPickupPoint = PickupPointFixture::getOne($deliveryService);

        $createService = new CreatePickupPointService($this->doctrinePickupPointRepository);
        $changeService = new ChangePickupPointService($this->doctrinePickupPointRepository, $createService);

        $this->doctrinePickupPointRepository->method('ofDeliveryServiceAndCode')->willReturn(null);
        $changeService->change(PickupPointDtoFixture::getOne($newPickupPoint->getDeliveryService()));

        $this->doctrinePickupPointRepository->method('ofId')->willReturn($newPickupPoint);
        $pickupPoint = $this->doctrinePickupPointRepository->ofId($newPickupPoint->getId());

        $this->assertNotNull($pickupPoint);
        $this->assertInstanceOf(PickupPoint::class, $pickupPoint);
        $this->assertEquals($newPickupPoint->getId(), $pickupPoint->getId());
        $this->assertEquals($newPickupPoint->getCode(), $pickupPoint->getCode());
        $this->assertEquals($newPickupPoint->getDeliveryService()->getId(), $pickupPoint->getDeliveryService()->getId());
        $this->assertEquals($newPickupPoint->getType(), $pickupPoint->getType());
        $this->assertEquals($newPickupPoint->getWeightMax(), $pickupPoint->getWeightMax());
        $this->assertEquals($newPickupPoint->getWeightMin(), $pickupPoint->getWeightMin());
        $this->assertNotNull($pickupPoint->getCreatedAt());
        $this->assertNull($pickupPoint->getUpdatedAt());
    }

    public function testChangeExist()
    {
        $address = AddressFixture::getOneFilled();
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');

        $newPickupPoint = PickupPointFixture::getOne($deliveryService);

        $createService = new CreatePickupPointService($this->doctrinePickupPointRepository);
        $changeService = new ChangePickupPointService($this->doctrinePickupPointRepository, $createService);

        $this->doctrinePickupPointRepository->method('ofDeliveryServiceAndCode')->willReturn($newPickupPoint);
        $changeService->change(PickupPointDtoFixture::getOne($newPickupPoint->getDeliveryService()));

        $this->doctrinePickupPointRepository->method('ofId')->willReturn($newPickupPoint);
        $pickupPoint = $this->doctrinePickupPointRepository->ofId($newPickupPoint->getId());

        $this->assertNotNull($pickupPoint);
        $this->assertInstanceOf(PickupPoint::class, $pickupPoint);
        $this->assertEquals($newPickupPoint->getId(), $pickupPoint->getId());
        $this->assertEquals($newPickupPoint->getCode(), $pickupPoint->getCode());
        $this->assertEquals($newPickupPoint->getDeliveryService()->getId(), $pickupPoint->getDeliveryService()->getId());
        $this->assertEquals($newPickupPoint->getType(), $pickupPoint->getType());
        $this->assertEquals($newPickupPoint->getWeightMax(), $pickupPoint->getWeightMax());
        $this->assertEquals($newPickupPoint->getWeightMin(), $pickupPoint->getWeightMin());
        $this->assertNotNull($pickupPoint->getCreatedAt());
        $this->assertNotNull($pickupPoint->getUpdatedAt());
    }
}
