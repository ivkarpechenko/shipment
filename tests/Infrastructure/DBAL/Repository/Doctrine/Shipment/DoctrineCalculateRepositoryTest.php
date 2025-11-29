<?php

namespace App\Tests\Infrastructure\DBAL\Repository\Doctrine\Shipment;

use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Domain\Shipment\Entity\Calculate;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use App\Infrastructure\DBAL\Repository\Doctrine\Shipment\DoctrineCalculateRepository;
use App\Infrastructure\DBAL\Repository\Doctrine\Shipment\DoctrineShipmentRepository;
use App\Infrastructure\DBAL\Repository\Doctrine\TariffPlan\DoctrineTariffPlanRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\Fixture\Shipment\CalculateFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;

class DoctrineCalculateRepositoryTest extends DoctrineTestCase
{
    protected DoctrineCalculateRepository $doctrineCalculateRepository;

    protected DoctrineShipmentRepository $doctrineShipmentRepository;

    protected DoctrineTariffPlanRepository $doctrineTariffPlanRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctrineCalculateRepository = $this->getContainer()->get(DoctrineCalculateRepository::class);
        $this->doctrineShipmentRepository = $this->getContainer()->get(DoctrineShipmentRepository::class);
        $this->doctrineTariffPlanRepository = $this->getContainer()->get(DoctrineTariffPlanRepository::class);
    }

    public function testCreateCalculate()
    {
        $shipmentId = $this->doctrineShipmentRepository->create($this->getShipment());
        $tariffPlan = $this->createTariffPlan();

        $shipment = $this->doctrineShipmentRepository->ofId($shipmentId);
        $tariffPlan = $this->doctrineTariffPlanRepository->ofId($tariffPlan->getId());

        $newCalculate = CalculateFixture::getOneFilled(
            $shipment,
            $tariffPlan,
            5,
            10,
            100.0,
            120.0
        );

        $this->doctrineCalculateRepository->create($newCalculate);

        $calculates = $this->doctrineCalculateRepository->ofShipmentIdNotExpired($shipment->getId());

        $calculate = reset($calculates);
        $this->assertNotNull($calculate);
        $this->assertInstanceOf(Calculate::class, $calculate);
        $this->assertInstanceOf(Shipment::class, $calculate->getShipment());
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

    public function testUpdateCalculate()
    {
        $shipmentId = $this->doctrineShipmentRepository->create($this->getShipment());
        $tariffPlan = $this->createTariffPlan();

        $shipment = $this->doctrineShipmentRepository->ofId($shipmentId);
        $tariffPlan = $this->doctrineTariffPlanRepository->ofId($tariffPlan->getId());

        $newCalculate = CalculateFixture::getOneFilled(
            $shipment,
            $tariffPlan,
            5,
            10,
            100.0,
            120.0
        );

        $this->doctrineCalculateRepository->create($newCalculate);

        $calculates = $this->doctrineCalculateRepository->ofShipmentIdNotExpired($shipment->getId());

        $calculate = reset($calculates);
        $this->assertNotNull($calculate);
        $this->assertInstanceOf(Calculate::class, $calculate);
        $this->assertInstanceOf(Shipment::class, $calculate->getShipment());
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

        $calculate->changeExpiredAt(new \DateTime('+2 hour'));

        $this->doctrineCalculateRepository->update($calculate);

        $calculates = $this->doctrineCalculateRepository->ofShipmentIdNotExpired($shipment->getId());

        $calculate = reset($calculates);
        $this->assertEquals(
            (new \DateTime('+2 hour'))->format('Y-m-d H:i'),
            $calculate->getExpiredAt()->format('Y-m-d H:i')
        );
    }

    public function testFindByShipmentId()
    {
        $shipmentId = $this->doctrineShipmentRepository->create($this->getShipment());
        $tariffPlan = $this->createTariffPlan();

        $shipment = $this->doctrineShipmentRepository->ofId($shipmentId);
        $tariffPlan = $this->doctrineTariffPlanRepository->ofId($tariffPlan->getId());

        $newCalculate = CalculateFixture::getOneFilled(
            $shipment,
            $tariffPlan,
            5,
            10,
            100.0,
            120.0
        );

        $this->doctrineCalculateRepository->create($newCalculate);

        $calculates = $this->doctrineCalculateRepository->ofShipmentIdNotExpired($shipment->getId());

        $calculate = reset($calculates);
        $this->assertNotNull($calculate);
        $this->assertInstanceOf(Calculate::class, $calculate);
        $this->assertInstanceOf(Shipment::class, $calculate->getShipment());
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

    public function testFindById()
    {
        $shipmentId = $this->doctrineShipmentRepository->create($this->getShipment());
        $tariffPlan = $this->createTariffPlan();

        $shipment = $this->doctrineShipmentRepository->ofId($shipmentId);
        $tariffPlan = $this->doctrineTariffPlanRepository->ofId($tariffPlan->getId());

        $newCalculate = CalculateFixture::getOneFilled(
            $shipment,
            $tariffPlan,
            5,
            10,
            100.0,
            120.0
        );

        $this->doctrineCalculateRepository->create($newCalculate);

        $calculates = $this->doctrineCalculateRepository->ofShipmentIdNotExpired($shipment->getId());

        $calculate = reset($calculates);
        $this->assertNotNull($calculate);
        $this->assertInstanceOf(Calculate::class, $calculate);
        $this->assertInstanceOf(Shipment::class, $calculate->getShipment());
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

    protected function getShipment(): Shipment
    {
        $container = $this->getContainer();

        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $countryRepository->create(CountryFixture::getOne('Russia', 'RU'));

        $regionRepository = $container->get(RegionRepositoryInterface::class);
        $regionRepository->create(
            RegionFixture::getOne(
                $countryRepository->ofCode('RU'),
                'Moscow',
                'msk'
            )
        );

        $cityRepository = $container->get(CityRepositoryInterface::class);
        $cityRepository->create(
            CityFixture::getOne(
                $regionRepository->ofCode('msk'),
                'city',
                'Moscow'
            )
        );

        $addressRepository = $container->get(AddressRepositoryInterface::class);
        $addressRepository->create(AddressFixture::getOneFilled(
            city: $cityRepository->ofTypeAndName('city', 'Moscow'),
            address: 'address'
        ));

        $contactRepository = $container->get(ContactRepositoryInterface::class);
        $contactRepository->create(ContactFixture::getOne('test@gmail.com', 'contact'));

        $currencyRepository = $container->get(CurrencyRepositoryInterface::class);
        $currencyRepository->create(CurrencyFixture::getOne('RUB', 810, 'Russian ruble'));

        return ShipmentFixture::getOne(
            $addressRepository->ofAddress('address'),
            $addressRepository->ofAddress('address'),
            $contactRepository->ofEmail('test@gmail.com'),
            $contactRepository->ofEmail('test@gmail.com'),
            $currencyRepository->ofCode('RUB'),
            new \DateTime('now'),
            new \DateTime('now'),
            new \DateTime('now')
        );
    }

    protected function createTariffPlan(): TariffPlan
    {
        $container = $this->getContainer();

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRepository = $container->get(DeliveryServiceRepositoryInterface::class);
        $deliveryServiceRepository->create($newDeliveryService);

        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $deliveryMethodRepository = $container->get(DeliveryMethodRepositoryInterface::class);
        $deliveryMethodRepository->create($newDeliveryMethod);

        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());
        $deliveryMethod = $deliveryMethodRepository->ofId($newDeliveryMethod->getId());

        $deliveryService->addDeliveryMethod($deliveryMethod);
        $deliveryServiceRepository->update($deliveryService);

        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());
        $deliveryMethod = $deliveryMethodRepository->ofId($newDeliveryMethod->getId());

        $newTariffPlan = TariffPlanFixture::getOne($deliveryService, $deliveryMethod, 'test', 'test');
        $tariffPlanRepository = $container->get(TariffPlanRepositoryInterface::class);
        $tariffPlanRepository->create($newTariffPlan);

        return $tariffPlanRepository->ofId($newTariffPlan->getId());
    }
}
