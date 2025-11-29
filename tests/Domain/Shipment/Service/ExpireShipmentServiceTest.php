<?php

namespace App\Tests\Domain\Shipment\Service;

use App\Domain\Shipment\Repository\CalculateRepositoryInterface;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use App\Domain\Shipment\Service\ExpireCalculateService;
use App\Tests\Fixture\Shipment\CalculateFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExpireShipmentServiceTest extends KernelTestCase
{
    protected ShipmentRepositoryInterface $shipmentRepository;

    protected CalculateRepositoryInterface $calculateRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shipmentRepository = $this->createMock(ShipmentRepositoryInterface::class);
        $this->calculateRepository = $this->createMock(CalculateRepositoryInterface::class);
    }

    public function testExpireCalculationResult()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->shipmentRepository
            ->method('ofId')
            ->willReturn($shipment);

        $calculate = CalculateFixture::getOneFilled($shipment);
        $this->calculateRepository
            ->method('ofShipmentIdNotExpired')
            ->willReturn([$calculate]);

        $service = new ExpireCalculateService(
            $this->shipmentRepository,
            $this->calculateRepository
        );

        $service->expire($shipment->getId());

        $this->calculateRepository
            ->expects($this->once())
            ->method('ofIdNotExpired')
            ->willReturn(null);

        $shipment = $this->calculateRepository->ofIdNotExpired($calculate->getId());

        $this->assertNull($shipment);
    }
}
