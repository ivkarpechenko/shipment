<?php

namespace App\Tests\Domain\PickupPoint\Service;

use App\Domain\PickupPoint\Entity\PickupPoint;
use App\Domain\PickupPoint\Service\CreatePickupPointService;
use App\Infrastructure\DBAL\Repository\Doctrine\PickupPoint\DoctrinePickupPointRepository;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\PickupPoint\PickupPointDtoFixture;
use App\Tests\Fixture\PickupPoint\PickupPointFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreatePickupPointServiceTest extends KernelTestCase
{
    protected DoctrinePickupPointRepository $doctrinePickupPointRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->doctrinePickupPointRepository = $this->createMock(DoctrinePickupPointRepository::class);
    }

    public function testCreate()
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');

        $newPickupPoint = PickupPointFixture::getOne($deliveryService);

        $this->doctrinePickupPointRepository->method('ofId')->willReturn($newPickupPoint);

        $createService = new CreatePickupPointService($this->doctrinePickupPointRepository);
        $createService->create(PickupPointDtoFixture::getOne($newPickupPoint->getDeliveryService()));

        $pickupPoint = $this->doctrinePickupPointRepository->ofId($newPickupPoint->getId());

        $this->assertNotNull($pickupPoint);
        $this->assertInstanceOf(PickupPoint::class, $pickupPoint);
        $this->assertEquals($newPickupPoint->getId(), $pickupPoint->getId());
        $this->assertEquals($newPickupPoint->getName(), $pickupPoint->getName());
        $this->assertEquals($newPickupPoint->getCode(), $pickupPoint->getCode());
        $this->assertEquals($newPickupPoint->getDeliveryService()->getId(), $pickupPoint->getDeliveryService()->getId());
        $this->assertEquals($newPickupPoint->getType(), $pickupPoint->getType());
        $this->assertEquals($newPickupPoint->getWeightMax(), $pickupPoint->getWeightMax());
        $this->assertEquals($newPickupPoint->getWeightMin(), $pickupPoint->getWeightMin());
        $this->assertNotNull($pickupPoint->getCreatedAt());
        $this->assertNull($pickupPoint->getUpdatedAt());
    }
}
