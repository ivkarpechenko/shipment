<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\DeliveryService\Dellin\Strategy;

use App\Domain\Shipment\Dto\CalculateDto;
use App\Domain\Shipment\Enum\CargoTypeEnum;
use App\Domain\Shipment\Service\CheckShipmentService;
use App\Domain\Tax\Service\CalculateTaxByCountryAndTotalSumService;
use App\Infrastructure\DeliveryService\Dellin\Service\DellinCalculateService;
use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinCalculateDto;
use App\Infrastructure\DeliveryService\Dellin\Strategy\DellinCalculateStrategy;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DellinCalculateStrategyTest extends KernelTestCase
{
    private DellinCalculateService $dellinCalculateServiceMock;

    private CalculateTaxByCountryAndTotalSumService $calculateTaxByCountryAndTotalSumServiceMock;

    private CheckShipmentService $checkShipmentServiceMock;

    private LoggerInterface $loggerMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dellinCalculateServiceMock = $this->createMock(DellinCalculateService::class);
        $this->calculateTaxByCountryAndTotalSumServiceMock = $this->createMock(CalculateTaxByCountryAndTotalSumService::class);
        $this->checkShipmentServiceMock = $this->createMock(CheckShipmentService::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
    }

    public function testExecute(): void
    {
        $this->dellinCalculateServiceMock->method('calculate')->willReturn(
            new DellinCalculateDto(
                derivalPrice: 100,
                arrivalPrice: 200,
                deliveryTerm: 1,
                insurance: 0,
                minPeriod: 1,
                maxPeriod: 2,
                deliverySum: 300,
                totalSum: 400
            )
        );

        $this->calculateTaxByCountryAndTotalSumServiceMock->method('calculate')->willReturn(
            520.00
        );

        $dellinCalculateStrategy = new DellinCalculateStrategy(
            $this->dellinCalculateServiceMock,
            $this->calculateTaxByCountryAndTotalSumServiceMock,
            $this->checkShipmentServiceMock,
            $this->loggerMock
        );

        $shipment = ShipmentFixture::getOneFilled();

        $calculateDto = $dellinCalculateStrategy->execute($shipment, TariffPlanFixture::getOneFilled());

        $this->assertInstanceOf(CalculateDto::class, $calculateDto);
        $this->assertEquals(1, $calculateDto->minPeriod);
        $this->assertEquals(4, $calculateDto->maxPeriod);
        $this->assertEquals(300, $calculateDto->deliveryCost);
        $this->assertEquals(400, $calculateDto->deliveryTotalCost);
        $this->assertEquals(520, $calculateDto->deliveryTotalCostTax);
    }

    public function testSupportByDeliveryServiceName(): void
    {
        $this->checkShipmentServiceMock->method('isEqualRegion')->willReturn(false);
        $this->checkShipmentServiceMock->method('getCargoTypes')->willReturn([]);

        $dellinCalculateStrategy = new DellinCalculateStrategy(
            $this->dellinCalculateServiceMock,
            $this->calculateTaxByCountryAndTotalSumServiceMock,
            $this->checkShipmentServiceMock,
            $this->loggerMock
        );

        $shipment = ShipmentFixture::getOneFilled();

        $this->assertTrue($dellinCalculateStrategy->supports('dellin', $shipment, TariffPlanFixture::getOneFilled()));
        $this->assertFalse($dellinCalculateStrategy->supports('cdek', $shipment, TariffPlanFixture::getOneFilled()));

        $this->expectException(\ValueError::class);
        $dellinCalculateStrategy->supports('test', $shipment, TariffPlanFixture::getOneFilled());
    }

    public function testSupportIfIsEqualRegion(): void
    {
        $this->checkShipmentServiceMock->method('isEqualRegion')->willReturn(true);
        $this->checkShipmentServiceMock->method('getCargoTypes')->willReturn([CargoTypeEnum::LARGE_SIZED]);

        $dellinCalculateStrategy = new DellinCalculateStrategy(
            $this->dellinCalculateServiceMock,
            $this->calculateTaxByCountryAndTotalSumServiceMock,
            $this->checkShipmentServiceMock,
            $this->loggerMock
        );

        $shipment = ShipmentFixture::getOneFilled();

        $this->assertTrue($dellinCalculateStrategy->supports('dellin', $shipment, TariffPlanFixture::getOneFilled()));
        $this->assertFalse($dellinCalculateStrategy->supports('cdek', $shipment, TariffPlanFixture::getOneFilled()));

        $this->expectException(\ValueError::class);
        $dellinCalculateStrategy->supports('test', $shipment, TariffPlanFixture::getOneFilled());
    }

    public function testSupportIfIsSmallSized(): void
    {
        $this->checkShipmentServiceMock->method('isEqualRegion')->willReturn(true);
        $this->checkShipmentServiceMock->method('getCargoTypes')->willReturn([CargoTypeEnum::SMALL_SIZED]);

        $dellinCalculateStrategy = new DellinCalculateStrategy(
            $this->dellinCalculateServiceMock,
            $this->calculateTaxByCountryAndTotalSumServiceMock,
            $this->checkShipmentServiceMock,
            $this->loggerMock
        );

        $shipment = ShipmentFixture::getOneFilled();

        $this->assertFalse($dellinCalculateStrategy->supports('dellin', $shipment, TariffPlanFixture::getOneFilled()));
        $this->assertFalse($dellinCalculateStrategy->supports('cdek', $shipment, TariffPlanFixture::getOneFilled()));

        $this->expectException(\ValueError::class);
        $dellinCalculateStrategy->supports('test', $shipment, TariffPlanFixture::getOneFilled());
    }

    public function testSupportIfIsEqualRegionAndIsSmallSized(): void
    {
        $this->checkShipmentServiceMock->method('isEqualRegion')->willReturn(true);
        $this->checkShipmentServiceMock->method('getCargoTypes')->willReturn([CargoTypeEnum::SMALL_SIZED]);

        $dellinCalculateStrategy = new DellinCalculateStrategy(
            $this->dellinCalculateServiceMock,
            $this->calculateTaxByCountryAndTotalSumServiceMock,
            $this->checkShipmentServiceMock,
            $this->loggerMock
        );

        $shipment = ShipmentFixture::getOneFilled();

        $this->assertFalse($dellinCalculateStrategy->supports('dellin', $shipment, TariffPlanFixture::getOneFilled()));
        $this->assertFalse($dellinCalculateStrategy->supports('cdek', $shipment, TariffPlanFixture::getOneFilled()));

        $this->expectException(\ValueError::class);
        $dellinCalculateStrategy->supports('test', $shipment, TariffPlanFixture::getOneFilled());
    }

    public function testSupportIfIsNotEqualRegionAndIsLargeSized(): void
    {
        $this->checkShipmentServiceMock->method('isEqualRegion')->willReturn(false);
        $this->checkShipmentServiceMock->method('getCargoTypes')->willReturn([CargoTypeEnum::LARGE_SIZED]);

        $dellinCalculateStrategy = new DellinCalculateStrategy(
            $this->dellinCalculateServiceMock,
            $this->calculateTaxByCountryAndTotalSumServiceMock,
            $this->checkShipmentServiceMock,
            $this->loggerMock
        );

        $shipment = ShipmentFixture::getOneFilled();

        $this->assertTrue($dellinCalculateStrategy->supports('dellin', $shipment, TariffPlanFixture::getOneFilled()));
        $this->assertFalse($dellinCalculateStrategy->supports('cdek', $shipment, TariffPlanFixture::getOneFilled()));

        $this->expectException(\ValueError::class);
        $dellinCalculateStrategy->supports('test', $shipment, TariffPlanFixture::getOneFilled());
    }
}
