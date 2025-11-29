<?php

namespace App\Tests\Domain\Shipment\Service;

use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\Shipment\Entity\Calculate;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Exception\ShipmentNotFoundException;
use App\Domain\Shipment\Repository\CalculateRepositoryInterface;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use App\Domain\Shipment\Service\CheckAddressInRestrictedAreaService;
use App\Domain\Shipment\Service\CreateCalculateService;
use App\Domain\Shipment\Strategy\CalculateContext;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\Shipment\CalculateDtoFixture;
use App\Tests\Fixture\Shipment\CalculateFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class CreateCalculateServiceTest extends KernelTestCase
{
    protected ShipmentRepositoryInterface $shipmentRepository;

    protected CalculateRepositoryInterface $calculateRepository;

    protected CalculateContext $calculateContext;

    protected DeliveryServiceRepositoryInterface $deliveryServiceRepository;

    protected DeliveryMethodRepositoryInterface $deliveryMethodRepository;

    protected TariffPlanRepositoryInterface $tariffPlanRepository;

    protected CheckAddressInRestrictedAreaService $checkAddressInRestrictedAreaService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shipmentRepository = $this->createMock(ShipmentRepositoryInterface::class);
        $this->calculateRepository = $this->createMock(CalculateRepositoryInterface::class);
        $this->calculateContext = $this->createMock(CalculateContext::class);
        $this->deliveryServiceRepository = $this->createMock(DeliveryServiceRepositoryInterface::class);
        $this->deliveryMethodRepository = $this->createMock(DeliveryMethodRepositoryInterface::class);
        $this->tariffPlanRepository = $this->createMock(TariffPlanRepositoryInterface::class);
        $this->checkAddressInRestrictedAreaService = $this->createMock(CheckAddressInRestrictedAreaService::class);
    }

    public function testCreateCalculate()
    {
        $shipment = ShipmentFixture::getOneFilled();
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $deliveryService->addDeliveryMethod($deliveryMethod);
        $tariffPlan = TariffPlanFixture::getOne($deliveryService, $deliveryMethod, 'test', 'test');

        $this->shipmentRepository->method('ofId')->willReturn($shipment);
        $this->deliveryServiceRepository->method('ofCode')->willReturn($deliveryService);
        $this->deliveryMethodRepository->method('ofCode')->willReturn($deliveryMethod);
        $this->tariffPlanRepository->method('ofId')->willReturn($tariffPlan);
        $this->checkAddressInRestrictedAreaService->method('check')->willReturn(true);

        $calculateDto = CalculateDtoFixture::getOne(
            1,
            1,
            100.0,
            120.0,
            22.0
        );
        $this->calculateContext->method('execute')->willReturn($calculateDto);

        $service = new CreateCalculateService(
            $this->shipmentRepository,
            $this->deliveryServiceRepository,
            $this->deliveryMethodRepository,
            $this->tariffPlanRepository,
            $this->calculateRepository,
            $this->calculateContext,
            $this->checkAddressInRestrictedAreaService
        );

        $service->create($shipment->getId(), $tariffPlan->getId());

        $this->calculateRepository->method('ofShipmentIdNotExpired')->willReturn(
            [
                CalculateFixture::getOne(
                    $shipment,
                    $tariffPlan,
                    $calculateDto->minPeriod,
                    $calculateDto->maxPeriod,
                    $calculateDto->deliveryCost,
                    $calculateDto->deliveryTotalCost,
                    $calculateDto->deliveryTotalCostTax
                )]
        );

        $calculates = $this->calculateRepository->ofShipmentIdNotExpired($shipment->getId());
        $calculate = reset($calculates);
        $this->assertNotNull($calculate);
        $this->assertInstanceOf(Calculate::class, $calculate);
        $this->assertInstanceOf(Shipment::class, $calculate->getShipment());
        $this->assertInstanceOf(TariffPlan::class, $calculate->getTariffPlan());
        $this->assertEquals(1, $calculate->getMinPeriod());
        $this->assertEquals(1, $calculate->getMaxPeriod());
        $this->assertEquals(100.0, $calculate->getDeliveryCost());
        $this->assertEquals(120.0, $calculate->getDeliveryTotalCost());
        $this->assertEquals(22.0, $calculate->getDeliveryTotalCostTax());
        $this->assertNotNull($calculate->getCreatedAt());
        $this->assertNotNull($calculate->getExpiredAt());
        $this->assertEquals(
            (new \DateTime('+1 hour'))->format('Y-m-d H:i'),
            $calculate->getExpiredAt()->format('Y-m-d H:i')
        );
    }

    public function testCreateCalculateIfShipmentNotFound()
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $tariffPlan = TariffPlanFixture::getOne($deliveryService, $deliveryMethod, 'test', 'test');

        $service = new CreateCalculateService(
            $this->shipmentRepository,
            $this->deliveryServiceRepository,
            $this->deliveryMethodRepository,
            $this->tariffPlanRepository,
            $this->calculateRepository,
            $this->calculateContext,
            $this->checkAddressInRestrictedAreaService
        );

        $this->deliveryServiceRepository->method('ofCode')->willReturn($deliveryService);
        $this->deliveryMethodRepository->method('ofCode')->willReturn($deliveryMethod);
        $this->tariffPlanRepository->method('ofId')->willReturn($tariffPlan);
        $this->checkAddressInRestrictedAreaService->method('check')->willReturn(true);

        $this->expectException(ShipmentNotFoundException::class);
        $service->create(Uuid::v1(), $tariffPlan->getId());
    }
}
