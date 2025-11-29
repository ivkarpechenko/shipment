<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\DeliveryService\CDEK\Strategy;

use App\Domain\Shipment\Dto\CalculateDto;
use App\Domain\Shipment\Enum\CargoTypeEnum;
use App\Domain\Shipment\Service\CheckShipmentService;
use App\Domain\Tax\Service\CalculateTaxByCountryAndTotalSumService;
use App\Infrastructure\DeliveryService\CDEK\Service\CdekCalculateService;
use App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto\CdekCalculateDto;
use App\Infrastructure\DeliveryService\CDEK\Strategy\CdekCalculateStrategy;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CdekCalculateStrategyTest extends KernelTestCase
{
    private CdekCalculateService $cdekCalculateServiceMock;

    private CalculateTaxByCountryAndTotalSumService $calculateTaxByCountryAndTotalSumServiceMock;

    private CheckShipmentService $checkShipmentServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cdekCalculateServiceMock = $this->createMock(CdekCalculateService::class);
        $this->calculateTaxByCountryAndTotalSumServiceMock = $this->createMock(CalculateTaxByCountryAndTotalSumService::class);
        $this->checkShipmentServiceMock = $this->createMock(CheckShipmentService::class);
    }

    public function testExecute(): void
    {
        $this->cdekCalculateServiceMock->method('calculate')->willReturn(
            new CdekCalculateDto(
                periodMin: 1,
                currency: 'RUB',
                deliverySum: 100,
                weightCalc: 1,
                periodMax: 2,
                totalSum: 120
            )
        );

        $this->calculateTaxByCountryAndTotalSumServiceMock->method('calculate')->willReturn(
            520.00
        );

        $cdekCalculateStrategy = new CdekCalculateStrategy(
            $this->cdekCalculateServiceMock,
            $this->calculateTaxByCountryAndTotalSumServiceMock,
            $this->checkShipmentServiceMock,
        );

        $shipment = ShipmentFixture::getOneFilled();

        $calculateDto = $cdekCalculateStrategy->execute($shipment, TariffPlanFixture::getOneFilled());

        $this->assertInstanceOf(CalculateDto::class, $calculateDto);
        $this->assertEquals(1, $calculateDto->minPeriod);
        $this->assertEquals(2, $calculateDto->maxPeriod);
        $this->assertEquals(100, $calculateDto->deliveryCost);
        $this->assertEquals(120, $calculateDto->deliveryTotalCost);
        $this->assertEquals(520, $calculateDto->deliveryTotalCostTax);
    }

    public function testSupportByDeliveryServiceName(): void
    {
        $this->checkShipmentServiceMock->method('isEqualRegion')->willReturn(false);
        $this->checkShipmentServiceMock->method('getCargoTypes')->willReturn([]);

        $shipment = ShipmentFixture::getOneFilled();

        $cdekCalculateStrategy = new CdekCalculateStrategy(
            $this->cdekCalculateServiceMock,
            $this->calculateTaxByCountryAndTotalSumServiceMock,
            $this->checkShipmentServiceMock,
        );

        $this->assertTrue($cdekCalculateStrategy->supports('cdek', $shipment, TariffPlanFixture::getOneFilled()));
        $this->assertFalse($cdekCalculateStrategy->supports('dellin', $shipment, TariffPlanFixture::getOneFilled()));

        $this->expectException(\ValueError::class);
        $cdekCalculateStrategy->supports('test', $shipment, TariffPlanFixture::getOneFilled());
    }

    public function testSupportIfIsNotEqualRegion(): void
    {
        $this->checkShipmentServiceMock->method('isEqualRegion')->willReturn(false);
        $this->checkShipmentServiceMock->method('getCargoTypes')->willReturn([CargoTypeEnum::SMALL_SIZED]);

        $shipment = ShipmentFixture::getOneFilled();

        $cdekCalculateStrategy = new CdekCalculateStrategy(
            $this->cdekCalculateServiceMock,
            $this->calculateTaxByCountryAndTotalSumServiceMock,
            $this->checkShipmentServiceMock,
        );

        $this->assertFalse($cdekCalculateStrategy->supports('cdek', $shipment, TariffPlanFixture::getOneFilled()));
        $this->assertFalse($cdekCalculateStrategy->supports('dellin', $shipment, TariffPlanFixture::getOneFilled()));

        $this->expectException(\ValueError::class);
        $cdekCalculateStrategy->supports('test', $shipment, TariffPlanFixture::getOneFilled());
    }

    public function testSupportIfIsNotSmallSizedCargo(): void
    {
        $this->checkShipmentServiceMock->method('isEqualRegion')->willReturn(true);
        $this->checkShipmentServiceMock->method('getCargoTypes')->willReturn([CargoTypeEnum::LARGE_SIZED]);

        $shipment = ShipmentFixture::getOneFilled();

        $cdekCalculateStrategy = new CdekCalculateStrategy(
            $this->cdekCalculateServiceMock,
            $this->calculateTaxByCountryAndTotalSumServiceMock,
            $this->checkShipmentServiceMock,
        );

        $this->assertFalse($cdekCalculateStrategy->supports('cdek', $shipment, TariffPlanFixture::getOneFilled()));
        $this->assertFalse($cdekCalculateStrategy->supports('dellin', $shipment, TariffPlanFixture::getOneFilled()));

        $this->expectException(\ValueError::class);
        $cdekCalculateStrategy->supports('test', $shipment, TariffPlanFixture::getOneFilled());
    }

    public function testSupportIfNotSmallSizedCargoAndIsNotEqualsRegion(): void
    {
        $this->checkShipmentServiceMock->method('isEqualRegion')->willReturn(false);
        $this->checkShipmentServiceMock->method('getCargoTypes')->willReturn([CargoTypeEnum::LARGE_SIZED]);

        $shipment = ShipmentFixture::getOneFilled();

        $cdekCalculateStrategy = new CdekCalculateStrategy(
            $this->cdekCalculateServiceMock,
            $this->calculateTaxByCountryAndTotalSumServiceMock,
            $this->checkShipmentServiceMock,
        );

        $this->assertFalse($cdekCalculateStrategy->supports('cdek', $shipment, TariffPlanFixture::getOneFilled()));
        $this->assertFalse($cdekCalculateStrategy->supports('dellin', $shipment, TariffPlanFixture::getOneFilled()));

        $this->expectException(\ValueError::class);
        $cdekCalculateStrategy->supports('test', $shipment, TariffPlanFixture::getOneFilled());
    }

    public function testSupportIfSmallSizedCargoAndIsEqualRegion(): void
    {
        $this->checkShipmentServiceMock->method('isEqualRegion')->willReturn(true);
        $this->checkShipmentServiceMock->method('getCargoTypes')->willReturn([CargoTypeEnum::SMALL_SIZED]);

        $shipment = ShipmentFixture::getOneFilled();

        $cdekCalculateStrategy = new CdekCalculateStrategy(
            $this->cdekCalculateServiceMock,
            $this->calculateTaxByCountryAndTotalSumServiceMock,
            $this->checkShipmentServiceMock,
        );

        $this->assertTrue($cdekCalculateStrategy->supports('cdek', $shipment, TariffPlanFixture::getOneFilled()));
        $this->assertFalse($cdekCalculateStrategy->supports('dellin', $shipment, TariffPlanFixture::getOneFilled()));

        $this->expectException(\ValueError::class);
        $cdekCalculateStrategy->supports('test', $shipment, TariffPlanFixture::getOneFilled());
    }
}
