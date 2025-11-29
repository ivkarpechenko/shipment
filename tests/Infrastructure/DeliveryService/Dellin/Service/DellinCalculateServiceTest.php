<?php

namespace App\Tests\Infrastructure\DeliveryService\Dellin\Service;

use App\Infrastructure\DeliveryService\Dellin\Exception\DellinCalculateErrorException;
use App\Infrastructure\DeliveryService\Dellin\Service\DellinCalculateService;
use App\Infrastructure\DeliveryService\Dellin\Service\Request\Dto\DellinShipmentDto;
use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinCalculateDto;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\DeliveryService\Dellin\DellinCalculateDtoFixture;
use App\Tests\Fixture\Shipment\PackageFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DellinCalculateServiceTest extends KernelTestCase
{
    public function testDellinCalculatePriceAndPeriod()
    {
        $serviceMock = $this->createMock(DellinCalculateService::class);
        $serviceMock->method('calculate')->willReturn(
            DellinCalculateDtoFixture::getOneFilled(
                3030.0,
                2510.0,
                0,
                740.0,
                5,
                7,
                10043.0,
                10043.0
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

        $dellinCalculateDto = $serviceMock->calculate(
            DellinShipmentDto::from($shipment, $tariffPlan)
        );

        $this->assertNotNull($dellinCalculateDto);
        $this->assertInstanceOf(DellinCalculateDto::class, $dellinCalculateDto);
        $this->assertEquals(3030.0, $dellinCalculateDto->derivalPrice);
        $this->assertEquals(2510.0, $dellinCalculateDto->arrivalPrice);
        $this->assertEquals(740.0, $dellinCalculateDto->insurance);
        $this->assertEquals(5, $dellinCalculateDto->minPeriod);
        $this->assertEquals(7, $dellinCalculateDto->maxPeriod);
        $this->assertEquals(10043.0, $dellinCalculateDto->deliverySum);
        $this->assertEquals(10043.0, $dellinCalculateDto->totalSum);
    }

    public function testDellinCalculatePriceAndPeriodWithInvalidParams()
    {
        $serviceMock = $this->createMock(DellinCalculateService::class);
        $serviceMock->method('calculate')->willThrowException(new DellinCalculateErrorException());

        $shipment = ShipmentFixture::getOneFilled(
            from: AddressFixture::getOneFilled(address: 'г Москва, р-н Вешняки, ул Юности, д 5'),
            to: AddressFixture::getOneFilled(address: 'Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1'),
            packages: [
                PackageFixture::getOne(100, 100, 100, 100, 5),
            ],
            psd: new \DateTime('+4 days')
        );

        $tariffPlan = TariffPlanFixture::getOneFilled();

        $this->expectException(DellinCalculateErrorException::class);
        $serviceMock->calculate(DellinShipmentDto::from(
            $shipment,
            $tariffPlan
        ));
    }

    public function testDellinCalculatePriceAndPeriodWithDeliveryTerm()
    {
        $serviceMock = $this->createMock(DellinCalculateService::class);
        $serviceMock->method('calculate')->willReturn(
            DellinCalculateDtoFixture::getOneFilled(
                3030.0,
                2510.0,
                10,
                740.0,
                5,
                7,
                10043.0,
                10043.0
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

        $dellinCalculateDto = $serviceMock->calculate(
            DellinShipmentDto::from($shipment, $tariffPlan)
        );

        $this->assertNotNull($dellinCalculateDto);
        $this->assertInstanceOf(DellinCalculateDto::class, $dellinCalculateDto);
        $this->assertEquals(3030.0, $dellinCalculateDto->derivalPrice);
        $this->assertEquals(2510.0, $dellinCalculateDto->arrivalPrice);
        $this->assertEquals(10, $dellinCalculateDto->deliveryTerm);
        $this->assertEquals(740.0, $dellinCalculateDto->insurance);
        $this->assertEquals(5, $dellinCalculateDto->minPeriod);
        $this->assertEquals(7, $dellinCalculateDto->maxPeriod);
        $this->assertEquals(10043.0, $dellinCalculateDto->deliverySum);
        $this->assertEquals(10043.0, $dellinCalculateDto->totalSum);
    }
}
