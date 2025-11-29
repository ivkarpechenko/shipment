<?php

namespace App\Tests\Infrastructure\DBAL\Repository\Doctrine\Shipment;

use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Domain\Shipment\Entity\Shipment;
use App\Infrastructure\DBAL\Repository\Doctrine\Shipment\DoctrineShipmentRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;

class DoctrineShipmentRepositoryTest extends DoctrineTestCase
{
    protected DoctrineShipmentRepository $doctrineShipmentRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctrineShipmentRepository = $this->getContainer()->get(DoctrineShipmentRepository::class);
    }

    public function testCreateShipment()
    {
        $this->assertEmpty($this->doctrineShipmentRepository->all());

        $newShipment = $this->getShipment();

        $shipmentId = $this->doctrineShipmentRepository->create($newShipment);

        $shipment = $this->doctrineShipmentRepository->ofId($shipmentId);

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertEquals('address', $shipment->getFrom()->getAddress());
        $this->assertEquals('address', $shipment->getTo()->getAddress());
        $this->assertEquals('test@gmail.com', $shipment->getSender()->getEmail());
        $this->assertEquals('test@gmail.com', $shipment->getRecipient()->getEmail());
        $this->assertEquals('RUB', $shipment->getCurrency()->getCode());
        $this->assertNotNull($shipment->getPsd());
        $this->assertNotNull($shipment->getPsdStartTime());
        $this->assertNotNull($shipment->getPsdEndTime());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNull($shipment->getUpdatedAt());
    }

    public function testUpdateShipment()
    {
        $this->assertEmpty($this->doctrineShipmentRepository->all());

        $newShipment = $this->getShipment();

        $this->assertNotNull($newShipment->getPsd());
        $this->assertEquals(
            (new \DateTime('now'))->format('Y-m-d'),
            $newShipment->getPsd()->format('Y-m-d')
        );

        $psd = new \DateTime('+1 day');
        $newShipment->changePsd($psd);

        $this->doctrineShipmentRepository->update($newShipment);

        $shipment = $this->doctrineShipmentRepository->ofId($newShipment->getId());

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertEquals('address', $shipment->getFrom()->getAddress());
        $this->assertEquals('address', $shipment->getTo()->getAddress());
        $this->assertEquals('test@gmail.com', $shipment->getSender()->getEmail());
        $this->assertEquals('test@gmail.com', $shipment->getRecipient()->getEmail());
        $this->assertEquals('RUB', $shipment->getCurrency()->getCode());
        $this->assertNotNull($shipment->getPsd());
        $this->assertEquals($psd->format('Y-m-d'), $shipment->getPsd()->format('Y-m-d'));
        $this->assertNotNull($shipment->getPsdStartTime());
        $this->assertNotNull($shipment->getPsdEndTime());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());
    }

    public function testFindById()
    {
        $this->assertEmpty($this->doctrineShipmentRepository->all());

        $newShipment = $this->getShipment();

        $shipmentId = $this->doctrineShipmentRepository->create($newShipment);

        $shipment = $this->doctrineShipmentRepository->ofId($shipmentId);

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertEquals('address', $shipment->getFrom()->getAddress());
        $this->assertEquals('address', $shipment->getTo()->getAddress());
        $this->assertEquals('test@gmail.com', $shipment->getSender()->getEmail());
        $this->assertEquals('test@gmail.com', $shipment->getRecipient()->getEmail());
        $this->assertEquals('RUB', $shipment->getCurrency()->getCode());
        $this->assertNotNull($shipment->getPsd());
        $this->assertNotNull($shipment->getPsdStartTime());
        $this->assertNotNull($shipment->getPsdEndTime());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNull($shipment->getUpdatedAt());
    }

    public function testOfShipments()
    {
        $this->assertEmpty($this->doctrineShipmentRepository->all());

        $newShipment = $this->getShipment();

        $shipmentId = $this->doctrineShipmentRepository->create($newShipment);

        $shipments = $this->doctrineShipmentRepository->ofShipments([$shipmentId]);

        $shipment = reset($shipments);

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertEquals('address', $shipment->getFrom()->getAddress());
        $this->assertEquals('address', $shipment->getTo()->getAddress());
        $this->assertEquals('test@gmail.com', $shipment->getSender()->getEmail());
        $this->assertEquals('test@gmail.com', $shipment->getRecipient()->getEmail());
        $this->assertEquals('RUB', $shipment->getCurrency()->getCode());
        $this->assertNotNull($shipment->getPsd());
        $this->assertNotNull($shipment->getPsdStartTime());
        $this->assertNotNull($shipment->getPsdEndTime());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNull($shipment->getUpdatedAt());
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
}
