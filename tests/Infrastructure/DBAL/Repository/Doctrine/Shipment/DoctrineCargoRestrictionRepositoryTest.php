<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\DBAL\Repository\Doctrine\Shipment;

use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Domain\Shipment\Entity\CargoRestriction;
use App\Domain\Shipment\Entity\CargoType;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use App\Infrastructure\DBAL\Repository\Doctrine\Shipment\DoctrineCargoRestrictionRepository;
use App\Infrastructure\DBAL\Repository\Doctrine\Shipment\DoctrineCargoTypeRepository;
use App\Infrastructure\DBAL\Repository\Doctrine\Shipment\DoctrineShipmentRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\Fixture\Shipment\CargoRestrictionFixture;
use App\Tests\Fixture\Shipment\CargoTypeFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;

class DoctrineCargoRestrictionRepositoryTest extends DoctrineTestCase
{
    private DoctrineCargoRestrictionRepository $cargoRestrictionRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cargoRestrictionRepository = $this->getContainer()->get(DoctrineCargoRestrictionRepository::class);
    }

    public function testCreate(): void
    {
        $this->assertEmpty($this->cargoRestrictionRepository->all());

        $cargoTypeRepository = $this->getContainer()->get(DoctrineCargoTypeRepository::class);
        $newCargoType = CargoTypeFixture::getOne('code', 'name');
        $cargoTypeRepository->create($newCargoType);

        $shipmentRepository = $this->getContainer()->get(DoctrineShipmentRepository::class);
        $newShipment = $this->getShipment();
        $shipmentRepository->create($newShipment);

        $cargoType = $cargoTypeRepository->ofId($newCargoType->getId());
        $shipment = $shipmentRepository->ofId($newShipment->getId());

        $newCargoRestriction = CargoRestrictionFixture::getOne(
            cargoType: $cargoType,
            shipment: $shipment,
            maxWidth: 100,
            maxHeight: 200,
            maxLength: 300,
            maxWeight: 400,
            maxVolume: 500,
            maxSumDimensions: 600
        );

        $this->cargoRestrictionRepository->create($newCargoRestriction);

        $cargoRestriction = $this->cargoRestrictionRepository->ofId($newCargoRestriction->getId());

        $this->assertNotNull($cargoRestriction);
        $this->assertInstanceOf(CargoRestriction::class, $cargoRestriction);
        $this->assertInstanceOf(CargoType::class, $cargoRestriction->getCargoType());
        $this->assertInstanceOf(Shipment::class, $cargoRestriction->getShipment());
        $this->assertEquals(100, $cargoRestriction->getMaxWidth());
        $this->assertEquals(200, $cargoRestriction->getMaxHeight());
        $this->assertEquals(300, $cargoRestriction->getMaxLength());
        $this->assertEquals(400, $cargoRestriction->getMaxWeight());
        $this->assertEquals(500, $cargoRestriction->getMaxVolume());
        $this->assertEquals(600, $cargoRestriction->getMaxSumDimensions());
        $this->assertNotNull($cargoRestriction->getCreatedAt());
        $this->assertNull($cargoRestriction->getUpdatedAt());
    }

    public function testAll(): void
    {
        $this->assertEmpty($this->cargoRestrictionRepository->all());

        $cargoTypeRepository = $this->getContainer()->get(DoctrineCargoTypeRepository::class);
        $newCargoType = CargoTypeFixture::getOne('code', 'name');
        $cargoTypeRepository->create($newCargoType);

        $shipmentRepository = $this->getContainer()->get(DoctrineShipmentRepository::class);
        $newShipment = $this->getShipment();
        $shipmentRepository->create($newShipment);

        $cargoType = $cargoTypeRepository->ofId($newCargoType->getId());
        $shipment = $shipmentRepository->ofId($newShipment->getId());

        $newCargoRestriction = CargoRestrictionFixture::getOne(
            cargoType: $cargoType,
            shipment: $shipment,
            maxWidth: 100,
            maxHeight: 200,
            maxLength: 300,
            maxWeight: 400,
            maxVolume: 500,
            maxSumDimensions: 600
        );

        $this->cargoRestrictionRepository->create($newCargoRestriction);

        $cargoRestrictions = $this->cargoRestrictionRepository->all();

        $this->assertCount(1, $cargoRestrictions);

        $cargoRestriction = $cargoRestrictions[0];

        $this->assertNotNull($cargoRestriction);
        $this->assertInstanceOf(CargoRestriction::class, $cargoRestriction);
        $this->assertInstanceOf(CargoType::class, $cargoRestriction->getCargoType());
        $this->assertInstanceOf(Shipment::class, $cargoRestriction->getShipment());
        $this->assertEquals(100, $cargoRestriction->getMaxWidth());
        $this->assertEquals(200, $cargoRestriction->getMaxHeight());
        $this->assertEquals(300, $cargoRestriction->getMaxLength());
        $this->assertEquals(400, $cargoRestriction->getMaxWeight());
        $this->assertEquals(500, $cargoRestriction->getMaxVolume());
        $this->assertEquals(600, $cargoRestriction->getMaxSumDimensions());
        $this->assertNotNull($cargoRestriction->getCreatedAt());
        $this->assertNull($cargoRestriction->getUpdatedAt());
    }

    public function testOfShipmentIdAndCargoTypeCode(): void
    {
        $this->assertEmpty($this->cargoRestrictionRepository->all());

        $cargoTypeRepository = $this->getContainer()->get(DoctrineCargoTypeRepository::class);
        $newCargoType = CargoTypeFixture::getOne('code', 'name');
        $cargoTypeRepository->create($newCargoType);

        $shipmentRepository = $this->getContainer()->get(DoctrineShipmentRepository::class);
        $newShipment = $this->getShipment();
        $shipmentRepository->create($newShipment);

        $cargoType = $cargoTypeRepository->ofId($newCargoType->getId());
        $shipment = $shipmentRepository->ofId($newShipment->getId());

        $newCargoRestriction = CargoRestrictionFixture::getOne(
            cargoType: $cargoType,
            shipment: $shipment,
            maxWidth: 100,
            maxHeight: 200,
            maxLength: 300,
            maxWeight: 400,
            maxVolume: 500,
            maxSumDimensions: 600
        );

        $this->cargoRestrictionRepository->create($newCargoRestriction);

        $cargoRestriction = $this->cargoRestrictionRepository->ofShipmentIdAndCargoTypeCode($shipment->getId(), $cargoType->getCode());

        $this->assertNotNull($cargoRestriction);
        $this->assertInstanceOf(CargoRestriction::class, $cargoRestriction);
        $this->assertInstanceOf(CargoType::class, $cargoRestriction->getCargoType());
        $this->assertEquals($cargoType->getCode(), $cargoRestriction->getCargoType()->getCode());
        $this->assertInstanceOf(Shipment::class, $cargoRestriction->getShipment());
        $this->assertEquals($shipment->getId(), $cargoRestriction->getShipment()->getId());
        $this->assertEquals($newCargoRestriction->getMaxWidth(), $cargoRestriction->getMaxWidth());
        $this->assertEquals($newCargoRestriction->getMaxHeight(), $cargoRestriction->getMaxHeight());
        $this->assertEquals($newCargoRestriction->getMaxLength(), $cargoRestriction->getMaxLength());
        $this->assertEquals($newCargoRestriction->getMaxWeight(), $cargoRestriction->getMaxWeight());
        $this->assertEquals($newCargoRestriction->getMaxVolume(), $cargoRestriction->getMaxVolume());
        $this->assertEquals($newCargoRestriction->getMaxSumDimensions(), $cargoRestriction->getMaxSumDimensions());
        $this->assertNotNull($cargoRestriction->getCreatedAt());
        $this->assertNull($cargoRestriction->getUpdatedAt());
    }

    public function testOfShipmentId(): void
    {
        $this->assertEmpty($this->cargoRestrictionRepository->all());

        $cargoTypeRepository = $this->getContainer()->get(DoctrineCargoTypeRepository::class);
        $newCargoType = CargoTypeFixture::getOne('code', 'name');
        $cargoTypeRepository->create($newCargoType);

        $shipmentRepository = $this->getContainer()->get(DoctrineShipmentRepository::class);
        $newShipment = $this->getShipment();
        $shipmentRepository->create($newShipment);

        $cargoType = $cargoTypeRepository->ofId($newCargoType->getId());
        $shipment = $shipmentRepository->ofId($newShipment->getId());

        $newCargoRestriction = CargoRestrictionFixture::getOne(
            cargoType: $cargoType,
            shipment: $shipment,
            maxWidth: 100,
            maxHeight: 200,
            maxLength: 300,
            maxWeight: 400,
            maxVolume: 500,
            maxSumDimensions: 600
        );

        $this->cargoRestrictionRepository->create($newCargoRestriction);

        $cargoRestrictions = $this->cargoRestrictionRepository->ofShipmentId($shipment->getId());

        $this->assertCount(1, $cargoRestrictions);

        $cargoRestriction = $cargoRestrictions[0];

        $this->assertNotNull($cargoRestriction);
        $this->assertInstanceOf(CargoRestriction::class, $cargoRestriction);
        $this->assertInstanceOf(CargoType::class, $cargoRestriction->getCargoType());
        $this->assertEquals($cargoType->getCode(), $cargoRestriction->getCargoType()->getCode());
        $this->assertInstanceOf(Shipment::class, $cargoRestriction->getShipment());
        $this->assertEquals($shipment->getId(), $cargoRestriction->getShipment()->getId());
        $this->assertEquals($newCargoRestriction->getMaxWidth(), $cargoRestriction->getMaxWidth());
        $this->assertEquals($newCargoRestriction->getMaxHeight(), $cargoRestriction->getMaxHeight());
        $this->assertEquals($newCargoRestriction->getMaxLength(), $cargoRestriction->getMaxLength());
        $this->assertEquals($newCargoRestriction->getMaxWeight(), $cargoRestriction->getMaxWeight());
        $this->assertEquals($newCargoRestriction->getMaxVolume(), $cargoRestriction->getMaxVolume());
        $this->assertEquals($newCargoRestriction->getMaxSumDimensions(), $cargoRestriction->getMaxSumDimensions());
        $this->assertNotNull($cargoRestriction->getCreatedAt());
        $this->assertNull($cargoRestriction->getUpdatedAt());
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

        $deliveryServiceRepository = $container->get(DeliveryServiceRepositoryInterface::class);
        $deliveryServiceRepository->create(DeliveryServiceFixture::getOne('dellin', 'Деловые линии'));

        $deliveryMethodRepository = $container->get(DeliveryMethodRepositoryInterface::class);
        $deliveryMethodRepository->create(
            DeliveryMethodFixture::getOne('test', 'test')
        );
        $tariffPlanRepository = $container->get(TariffPlanRepositoryInterface::class);
        $tariffPlanRepository->create(
            TariffPlanFixture::getOne(
                $deliveryServiceRepository->ofCode('dellin'),
                $deliveryMethodRepository->ofCode('test'),
                'express',
                'Экспресс'
            )
        );

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
}
