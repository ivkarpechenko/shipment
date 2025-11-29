<?php

namespace App\Tests\Application\Shipment\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Shipment\Command\UpdateShipmentCommand;
use App\Application\Shipment\Command\UpdateShipmentCommandHandler;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use App\Tests\Application\MessengerCommandBusTest;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\Fixture\Shipment\UpdateShipmentDtoFixture;
use Symfony\Component\Uid\Uuid;

class UpdateShipmentCommandTest extends MessengerCommandBusTest
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new UpdateShipmentCommand(Uuid::v1(), UpdateShipmentDtoFixture::getOneFilled())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(UpdateShipmentCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new UpdateShipmentCommand(Uuid::v1(), UpdateShipmentDtoFixture::getOneFilled());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testUpdateShipmentCommandHandler()
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

        $currencyRepository = $container->get(CurrencyRepositoryInterface::class);
        $currencyRepository->create(CurrencyFixture::getOne('RUB', 810, 'Russian ruble'));

        $newShipment = ShipmentFixture::getOne(
            $addressRepository->ofAddress('address'),
            $addressRepository->ofAddress('address'),
            $contactRepository->ofEmail('test@gmail.com'),
            $contactRepository->ofEmail('test@gmail.com'),
            $currencyRepository->ofCode('RUB'),
            new \DateTime('now'),
            new \DateTime('now'),
            new \DateTime('now')
        );
        $container->get(ShipmentRepositoryInterface::class)->create($newShipment);

        $addressRepository->create(AddressFixture::getOneFilled(
            city: $cityRepository->ofTypeAndName('city', 'Moscow'),
            address: 'updated address'
        ));
        $container->get(UpdateShipmentCommandHandler::class)(
            new UpdateShipmentCommand($newShipment->getId(), UpdateShipmentDtoFixture::getOne(
                from: 'updated address'
            ))
        );

        $shipment = $container->get(ShipmentRepositoryInterface::class)->ofId($newShipment->getId());

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertEquals('updated address', $shipment->getFrom()->getAddress());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());
    }
}
