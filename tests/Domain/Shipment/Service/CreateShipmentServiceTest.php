<?php

namespace App\Tests\Domain\Shipment\Service;

use App\Domain\Address\Exception\AddressNotFoundException;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\Contact\Exception\ContactNotFoundException;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Currency\Exception\CurrencyDeactivatedException;
use App\Domain\Currency\Exception\CurrencyNotFoundException;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Exception\InvalidPsdException;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use App\Domain\Shipment\Service\CheckAddressInRestrictedAreaService;
use App\Domain\Shipment\Service\CreateShipmentService;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\Shipment\PackageFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class CreateShipmentServiceTest extends KernelTestCase
{
    protected ShipmentRepositoryInterface $shipmentRepository;

    protected AddressRepositoryInterface $addressRepository;

    protected ContactRepositoryInterface $contactRepository;

    protected CurrencyRepositoryInterface $currencyRepository;

    protected CheckAddressInRestrictedAreaService $checkAddressInRestrictedAreaService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shipmentRepository = $this->createMock(ShipmentRepositoryInterface::class);
        $this->addressRepository = $this->createMock(AddressRepositoryInterface::class);
        $this->contactRepository = $this->createMock(ContactRepositoryInterface::class);
        $this->currencyRepository = $this->createMock(CurrencyRepositoryInterface::class);
        $this->checkAddressInRestrictedAreaService = $this->createMock(CheckAddressInRestrictedAreaService::class);
    }

    public function testCreateShipment()
    {
        $address = AddressFixture::getOneFilled();
        $contact = ContactFixture::getOne('test@gmail.com', 'test');
        $currency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $package = PackageFixture::getOne(2, 1, 1, 1, 1);

        $this->addressRepository
            ->method('ofAddress')
            ->willReturn($address);
        $this->contactRepository
            ->method('ofId')
            ->willReturn($contact);
        $this->currencyRepository
            ->method('ofCode')
            ->willReturn($currency);

        $service = new CreateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->checkAddressInRestrictedAreaService
        );

        $shipmentId = $service->create(
            $address->getAddress(),
            $address->getAddress(),
            $contact->getId(),
            $contact->getId(),
            $currency->getId(),
            [
                $package,
            ],
            new \DateTime('now'),
            new \DateTime('now'),
            new \DateTime('now')
        );

        $this->assertNotNull($shipmentId);
        $this->assertInstanceOf(Uuid::class, $shipmentId);

        $this->shipmentRepository
            ->method('ofId')
            ->willReturn(ShipmentFixture::getOne(
                $address,
                $address,
                $contact,
                $contact,
                $currency,
                new \DateTime('now'),
                new \DateTime('now'),
                new \DateTime('now')
            ));

        $shipment = $this->shipmentRepository->ofId($shipmentId);

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertEquals($address, $shipment->getFrom());
        $this->assertEquals($address, $shipment->getTo());
        $this->assertEquals($contact, $shipment->getSender());
        $this->assertEquals($contact, $shipment->getRecipient());
        $this->assertEquals($currency, $shipment->getCurrency());
        $this->assertNotNull($shipment->getPsd());
        $this->assertNotNull($shipment->getPsdStartTime());
        $this->assertNotNull($shipment->getPsdEndTime());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNull($shipment->getUpdatedAt());
    }

    public function testCreateShipmentIfInvalidAddress()
    {
        $address = AddressFixture::getOneFilled();
        $contact = ContactFixture::getOne('test@gmail.com', 'test');

        $currency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $package = PackageFixture::getOne(1, 1, 1, 1, 1);

        $this->contactRepository
            ->method('ofId')
            ->willReturn($contact);

        $this->currencyRepository
            ->method('ofCode')
            ->willReturn($currency);

        $service = new CreateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->checkAddressInRestrictedAreaService
        );

        $this->expectException(AddressNotFoundException::class);
        $service->create(
            $address->getAddress(),
            $address->getAddress(),
            $contact->getId(),
            $contact->getId(),
            $currency->getId(),
            [
                $package,
            ],
            new \DateTime('now'),
            new \DateTime('now'),
            new \DateTime('now')
        );
    }

    public function testCreateShipmentIfNotFoundContact()
    {
        $address = AddressFixture::getOneFilled();
        $contact = ContactFixture::getOne('test@gmail.com', 'test');
        $currency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $package = PackageFixture::getOne(1, 1, 1, 1, 1);

        $this->addressRepository
            ->method('ofAddress')
            ->willReturn($address);

        $this->currencyRepository
            ->method('ofCode')
            ->willReturn($currency);

        $service = new CreateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->checkAddressInRestrictedAreaService
        );

        $this->expectException(ContactNotFoundException::class);
        $service->create(
            $address->getAddress(),
            $address->getAddress(),
            $contact->getId(),
            $contact->getId(),
            $currency->getId(),
            [
                $package,
            ],
            new \DateTime('now'),
            new \DateTime('now'),
            new \DateTime('now')
        );
    }

    public function testCreateShipmentIfNotFoundCurrency()
    {
        $address = AddressFixture::getOneFilled();
        $contact = ContactFixture::getOne('test@gmail.com', 'test');
        $currency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $package = PackageFixture::getOne(1, 1, 1, 1, 1);

        $this->addressRepository
            ->method('ofAddress')
            ->willReturn($address);

        $this->contactRepository
            ->method('ofId')
            ->willReturn($contact);

        $service = new CreateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->checkAddressInRestrictedAreaService
        );

        $this->expectException(CurrencyNotFoundException::class);
        $service->create(
            $address->getAddress(),
            $address->getAddress(),
            $contact->getId(),
            $contact->getId(),
            $currency->getId(),
            [
                $package,
            ],
            new \DateTime('now'),
            new \DateTime('now'),
            new \DateTime('now')
        );
    }

    public function testCreateShipmentIfDeactivatedCurrency()
    {
        $address = AddressFixture::getOneFilled();
        $contact = ContactFixture::getOne('test@gmail.com', 'test');
        $currency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $package = PackageFixture::getOne(1, 1, 1, 1, 1);

        $this->addressRepository
            ->method('ofAddress')
            ->willReturn($address);

        $this->contactRepository
            ->method('ofId')
            ->willReturn($contact);

        $this->currencyRepository
            ->method('ofCodeDeactivated')
            ->willReturn($currency);

        $service = new CreateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->checkAddressInRestrictedAreaService
        );

        $this->expectException(CurrencyDeactivatedException::class);
        $service->create(
            $address->getAddress(),
            $address->getAddress(),
            $contact->getId(),
            $contact->getId(),
            $currency->getId(),
            [
                $package,
            ],
            new \DateTime('now'),
            new \DateTime('now'),
            new \DateTime('now')
        );
    }

    public function testCreateShipmentIfPsdPastDate()
    {
        $address = AddressFixture::getOneFilled();
        $contact = ContactFixture::getOne('test@gmail.com', 'test');
        $currency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $package = PackageFixture::getOne(1, 1, 1, 1, 1);

        $this->addressRepository
            ->method('ofAddress')
            ->willReturn($address);

        $this->contactRepository
            ->method('ofId')
            ->willReturn($contact);

        $this->currencyRepository
            ->method('ofCode')
            ->willReturn($currency);

        $this->checkAddressInRestrictedAreaService
            ->method('check')
            ->willReturn(false);

        $service = new CreateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->checkAddressInRestrictedAreaService
        );

        $this->expectException(InvalidPsdException::class);
        $service->create(
            $address->getAddress(),
            $address->getAddress(),
            $contact->getId(),
            $contact->getId(),
            $currency->getId(),
            [
                $package,
            ],
            (new \DateTime('now'))->modify('-1 day'),
            new \DateTime('now'),
            new \DateTime('now')
        );
    }
}
