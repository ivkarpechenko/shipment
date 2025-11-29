<?php

namespace App\Tests\Domain\Shipment\Entity;

use App\Domain\Shipment\Entity\Calculate;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Tests\Fixture\Shipment\CalculateFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CalculateTest extends KernelTestCase
{
    public function testCreateCalculate()
    {
        $calculate = CalculateFixture::getOneFilled(
            minPeriod: 5,
            maxPeriod: 10,
            deliveryCost: 100.0,
            deliveryTotalCost: 120.0
        );

        $this->assertNotNull($calculate);
        $this->assertInstanceOf(Calculate::class, $calculate);
        $this->assertInstanceOf(Shipment::class, $calculate->getShipment());
        $this->assertInstanceOf(TariffPlan::class, $calculate->getTariffPlan());
        $this->assertEquals(5, $calculate->getMinPeriod());
        $this->assertEquals(10, $calculate->getMaxPeriod());
        $this->assertEquals(100.0, $calculate->getDeliveryCost());
        $this->assertEquals(120.0, $calculate->getDeliveryTotalCost());
        $this->assertNotNull($calculate->getCreatedAt());
        $this->assertNotNull($calculate->getExpiredAt());
        $this->assertEquals(
            (new \DateTime('+1 hour'))->format('Y-m-d H:i'),
            $calculate->getExpiredAt()->format('Y-m-d H:i')
        );
    }

    public function testUpdateCalculateExpiredAt()
    {
        $calculate = CalculateFixture::getOneFilled(
            minPeriod: 5,
            maxPeriod: 10,
            deliveryCost: 100.0,
            deliveryTotalCost: 120.0
        );

        $this->assertNotNull($calculate);
        $this->assertInstanceOf(Calculate::class, $calculate);
        $this->assertInstanceOf(Shipment::class, $calculate->getShipment());
        $this->assertInstanceOf(TariffPlan::class, $calculate->getTariffPlan());
        $this->assertEquals(5, $calculate->getMinPeriod());
        $this->assertEquals(10, $calculate->getMaxPeriod());
        $this->assertEquals(100.0, $calculate->getDeliveryCost());
        $this->assertEquals(120.0, $calculate->getDeliveryTotalCost());
        $this->assertNotNull($calculate->getCreatedAt());
        $this->assertNotNull($calculate->getExpiredAt());
        $this->assertEquals(
            (new \DateTime('+1 hour'))->format('Y-m-d H:i'),
            $calculate->getExpiredAt()->format('Y-m-d H:i')
        );

        $calculate->changeExpiredAt(new \DateTime('+2 hours'));

        $this->assertEquals(
            (new \DateTime('+2 hour'))->format('Y-m-d H:i'),
            $calculate->getExpiredAt()->format('Y-m-d H:i')
        );
    }
}
