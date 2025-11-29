<?php

namespace App\Tests\Domain\Shipment\Service;

use App\Domain\Address\Exception\AddressNotFoundException;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Currency\Exception\CurrencyDeactivatedException;
use App\Domain\Currency\Exception\CurrencyNotFoundException;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\PickupPoint\Repository\PickupPointRepositoryInterface;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Exception\ShipmentNotFoundException;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use App\Domain\Shipment\Service\UpdateShipmentService;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UpdateShipmentServiceTest extends KernelTestCase
{
    protected ShipmentRepositoryInterface $shipmentRepository;

    protected AddressRepositoryInterface $addressRepository;

    protected ContactRepositoryInterface $contactRepository;

    protected CurrencyRepositoryInterface $currencyRepository;

    protected PickupPointRepositoryInterface $pickupPointRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shipmentRepository = $this->createMock(ShipmentRepositoryInterface::class);
        $this->addressRepository = $this->createMock(AddressRepositoryInterface::class);
        $this->contactRepository = $this->createMock(ContactRepositoryInterface::class);
        $this->currencyRepository = $this->createMock(CurrencyRepositoryInterface::class);
        $this->pickupPointRepository = $this->createMock(PickupPointRepositoryInterface::class);
    }

    public function testUpdateShipmentFromAddress()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->shipmentRepository
            ->method('ofId')
            ->willReturn($shipment);

        $updateFromAddress = AddressFixture::getOneFilled(address: 'test address');
        $this->addressRepository
            ->method('ofAddress')
            ->willReturn($updateFromAddress);

        $service = new UpdateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->pickupPointRepository
        );

        $service->update($shipment->getId(), from: $updateFromAddress->getAddress());

        $this->shipmentRepository
            ->expects($this->once())
            ->method('ofId')
            ->willReturn(ShipmentFixture::getOne(
                $updateFromAddress,
                $shipment->getTo(),
                $shipment->getSender(),
                $shipment->getRecipient(),
                $shipment->getCurrency(),
                new \DateTime('now'),
                new \DateTime('now'),
                new \DateTime('now')
            ));

        $shipment = $this->shipmentRepository->ofId($shipment->getId());

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertEquals($updateFromAddress, $shipment->getFrom());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());
    }

    public function testUpdateShipmentToAddress()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->shipmentRepository
            ->method('ofId')
            ->willReturn($shipment);

        $updateToAddress = AddressFixture::getOneFilled(address: 'test address');
        $this->addressRepository
            ->method('ofAddress')
            ->willReturn($updateToAddress);

        $service = new UpdateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->pickupPointRepository
        );

        $service->update($shipment->getId(), to: $updateToAddress->getAddress());

        $this->shipmentRepository
            ->expects($this->once())
            ->method('ofId')
            ->willReturn(ShipmentFixture::getOne(
                $shipment->getFrom(),
                $updateToAddress,
                $shipment->getSender(),
                $shipment->getRecipient(),
                $shipment->getCurrency(),
                new \DateTime('now'),
                new \DateTime('now'),
                new \DateTime('now')
            ));

        $shipment = $this->shipmentRepository->ofId($shipment->getId());

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertEquals($updateToAddress, $shipment->getTo());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());
    }

    public function testUpdateShipmentSender()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->shipmentRepository
            ->method('ofId')
            ->willReturn($shipment);

        $sender = ContactFixture::getOne('updatedtest@gmail.com', 'updated test');
        $this->contactRepository
            ->method('ofId')
            ->willReturn($sender);

        $service = new UpdateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->pickupPointRepository
        );

        $service->update($shipment->getId(), senderId: $sender->getId());

        $this->shipmentRepository
            ->expects($this->once())
            ->method('ofId')
            ->willReturn(ShipmentFixture::getOne(
                $shipment->getFrom(),
                $shipment->getTo(),
                $sender,
                $shipment->getRecipient(),
                $shipment->getCurrency(),
                new \DateTime('now'),
                new \DateTime('now'),
                new \DateTime('now')
            ));

        $shipment = $this->shipmentRepository->ofId($shipment->getId());

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertEquals($sender, $shipment->getSender());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());
    }

    public function testUpdateShipmentRecipient()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->shipmentRepository
            ->method('ofId')
            ->willReturn($shipment);

        $recipient = ContactFixture::getOne('updatedtest@gmail.com', 'updated test');
        $this->contactRepository
            ->method('ofId')
            ->willReturn($recipient);

        $service = new UpdateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->pickupPointRepository
        );

        $service->update($shipment->getId(), recipientId: $recipient->getId());

        $this->shipmentRepository
            ->expects($this->once())
            ->method('ofId')
            ->willReturn(ShipmentFixture::getOne(
                $shipment->getFrom(),
                $shipment->getTo(),
                $shipment->getSender(),
                $recipient,
                $shipment->getCurrency(),
                new \DateTime('now'),
                new \DateTime('now'),
                new \DateTime('now')
            ));

        $shipment = $this->shipmentRepository->ofId($shipment->getId());

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertEquals($recipient, $shipment->getRecipient());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());
    }

    public function testUpdateShipmentCurrency()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->shipmentRepository
            ->method('ofId')
            ->willReturn($shipment);

        $currency = CurrencyFixture::getOne('RUB', 810, 'Updated russian ruble');
        $this->currencyRepository
            ->method('ofCode')
            ->willReturn($currency);

        $service = new UpdateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->pickupPointRepository
        );

        $service->update($shipment->getId(), currencyCode: $currency->getCode());

        $this->shipmentRepository
            ->expects($this->once())
            ->method('ofId')
            ->willReturn(ShipmentFixture::getOne(
                $shipment->getFrom(),
                $shipment->getTo(),
                $shipment->getSender(),
                $shipment->getRecipient(),
                $currency,
                new \DateTime('now'),
                new \DateTime('now'),
                new \DateTime('now')
            ));

        $shipment = $this->shipmentRepository->ofId($shipment->getId());

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertEquals($currency, $shipment->getCurrency());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());
    }

    public function testUpdateShipmentIfNotFound()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $service = new UpdateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->pickupPointRepository
        );

        $this->expectException(ShipmentNotFoundException::class);
        $service->update($shipment->getId(), psd: new \DateTime('+1 day'));
    }

    public function testUpdateShipmentIfFromAddressNotFound()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->shipmentRepository
            ->method('ofId')
            ->willReturn($shipment);

        $service = new UpdateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->pickupPointRepository
        );

        $this->expectException(AddressNotFoundException::class);
        $service->update($shipment->getId(), from: 'test address');
    }

    public function testUpdateShipmentIfToAddressNotFound()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->shipmentRepository
            ->method('ofId')
            ->willReturn($shipment);

        $service = new UpdateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->pickupPointRepository
        );

        $this->expectException(AddressNotFoundException::class);
        $service->update($shipment->getId(), to: 'test address');
    }

    public function testUpdateShipmentIfCurrencyNotFound()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->shipmentRepository
            ->method('ofId')
            ->willReturn($shipment);

        $service = new UpdateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->pickupPointRepository
        );

        $this->expectException(CurrencyNotFoundException::class);
        $service->update($shipment->getId(), currencyCode: 'RUB');
    }

    public function testUpdateShipmentIfCurrencyDeactivated()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->shipmentRepository
            ->method('ofId')
            ->willReturn($shipment);

        $currency = CurrencyFixture::getOneDeactivated('RUB', 810, 'Russian ruble', isActive: false);
        $this->currencyRepository
            ->method('ofCodeDeactivated')
            ->willReturn($currency);

        $service = new UpdateShipmentService(
            $this->addressRepository,
            $this->contactRepository,
            $this->currencyRepository,
            $this->shipmentRepository,
            $this->pickupPointRepository
        );

        $this->expectException(CurrencyDeactivatedException::class);
        $service->update($shipment->getId(), currencyCode: $currency->getCode());
    }
}
