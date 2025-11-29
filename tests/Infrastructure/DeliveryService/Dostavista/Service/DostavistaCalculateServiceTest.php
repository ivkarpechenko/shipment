<?php

namespace App\Tests\Infrastructure\DeliveryService\Dostavista\Service;

use App\Infrastructure\DeliveryService\Dostavista\Exception\DostavistaCalculateErrorException;
use App\Infrastructure\DeliveryService\Dostavista\Service\DostavistaCalculateService;
use App\Infrastructure\DeliveryService\Dostavista\Service\Request\Dto\DostavistaShipmentDto;
use App\Infrastructure\DeliveryService\Dostavista\Service\Response\Dto\DostavistaCalculateDto;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\DeliveryService\Dostavista\DostavistaCalculateDtoFixture;
use App\Tests\Fixture\Shipment\PackageFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DostavistaCalculateServiceTest extends KernelTestCase
{
    public function testDostavistaCalculatePriceAndPeriod()
    {
        $serviceMock = $this->createMock(DostavistaCalculateService::class);
        $serviceMock->method('calculate')->willReturn(
            DostavistaCalculateDtoFixture::getOneFilled(
                3,
                12,
                1200,
                1000
            )
        );

        $shipment = ShipmentFixture::getOneFilled(
            from: AddressFixture::getOneFilled(address: 'г Москва, р-н Вешняки, ул Юности, д 5'),
            to: AddressFixture::getOneFilled(address: 'Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1'),
            packages: [
                PackageFixture::getOne(100, 100, 100, 100, 5),
            ],
            psd: new \DateTime('+4 days')
        );
        $tariffPlan = TariffPlanFixture::getOneFilled();

        $dostavistaCalculateDto = $serviceMock->calculate(
            DostavistaShipmentDto::from($shipment, $tariffPlan)
        );

        $this->assertNotNull($dostavistaCalculateDto);
        $this->assertInstanceOf(DostavistaCalculateDto::class, $dostavistaCalculateDto);
        $this->assertEquals(3, $dostavistaCalculateDto->minPeriod);
        $this->assertEquals(12, $dostavistaCalculateDto->maxPeriod);
        $this->assertEquals(1200, $dostavistaCalculateDto->paymentAmount);
        $this->assertEquals(1000, $dostavistaCalculateDto->deliveryFeeAmount);
    }

    public function testDostavistaCalculatePriceAndPeriodWithInvalidParams()
    {
        $serviceMock = $this->createMock(DostavistaCalculateService::class);
        $serviceMock->method('calculate')->willThrowException(new DostavistaCalculateErrorException());

        $shipment = ShipmentFixture::getOneFilled(
            from: AddressFixture::getOneFilled(address: 'Москва, Юности, 5'),
            to: AddressFixture::getOneFilled(address: 'Белгородская обл, г Алексеевка, ул Слободская, д 1/1'),
            packages: [
                PackageFixture::getOne(100, 100, 100, 100, 5),
            ],
            psd: new \DateTime('+4 days')
        );
        $tariffPlan = TariffPlanFixture::getOneFilled();

        $this->expectException(DostavistaCalculateErrorException::class);
        $serviceMock->calculate(DostavistaShipmentDto::from($shipment, $tariffPlan));
    }
}
