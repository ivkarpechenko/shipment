<?php

namespace App\Tests\Infrastructure\DeliveryService\CDEK\Service;

use App\Infrastructure\DeliveryService\CDEK\Service\CdekCalculateService;
use App\Infrastructure\DeliveryService\CDEK\Service\Exception\CdekCalculateErrorException;
use App\Infrastructure\DeliveryService\CDEK\Service\Request\Dto\CdekShipmentDto;
use App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto\CdekCalculateDto;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use App\Tests\HttpTestCase;

class TestCdekCalculateService extends HttpTestCase
{
    public function testCalculatePriceAndPeriod()
    {
        $shipment = ShipmentFixture::getOneFilled();
        $tariffPlan = TariffPlanFixture::getOneFilled();

        $cdekHttpClientService = $this->createMock(CdekCalculateService::class);
        $cdekHttpClientService->method('calculate')
            ->willReturn(new CdekCalculateDto(8, 'RUB', 13211, 32112, 12, 14232));

        $cdekShipmentCalculateDto = $cdekHttpClientService->calculate(CdekShipmentDto::fromShipmentAndTariffPlan($shipment, $tariffPlan));

        $this->assertNotNull($cdekShipmentCalculateDto);
        $this->assertIsFloat($cdekShipmentCalculateDto->deliverySum);
        $this->assertIsFloat($cdekShipmentCalculateDto->totalSum);
        $this->assertIsInt($cdekShipmentCalculateDto->periodMax);
        $this->assertIsInt($cdekShipmentCalculateDto->periodMin);
        $this->assertIsInt($cdekShipmentCalculateDto->weightCalc);
        $this->assertIsString($cdekShipmentCalculateDto->currency);
    }

    public function testCalculatePriceAndPeriodWithInvalidParams()
    {
        $shipment = ShipmentFixture::getOneFilled();
        $tariffPlan = TariffPlanFixture::getOneFilled();

        $cdekHttpClientService = $this->createMock(CdekCalculateService::class);
        $cdekHttpClientService->method('calculate')
            ->willThrowException(new CdekCalculateErrorException());

        $this->expectException(CdekCalculateErrorException::class);
        $cdekHttpClientService->calculate(CdekShipmentDto::fromShipmentAndTariffPlan($shipment, $tariffPlan));
    }
}
