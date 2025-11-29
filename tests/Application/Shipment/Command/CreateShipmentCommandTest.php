<?php

namespace App\Tests\Application\Shipment\Command;

use App\Application\Address\Query\External\FindExternalAddressInterface;
use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Shipment\Command\CreateShipmentCommand;
use App\Application\Shipment\Command\CreateShipmentCommandHandler;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use App\Domain\Shipment\Service\CheckAddressInRestrictedAreaService;
use App\Tests\Fixture\Address\AddressDtoFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\Shipment\CreateShipmentDtoFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class CreateShipmentCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new CreateShipmentCommand(CreateShipmentDtoFixture::getOneFilled())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateShipmentCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new CreateShipmentCommand(CreateShipmentDtoFixture::getOneFilled());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateShipmentCommandHandler()
    {
        $container = $this->getContainer();

        $addressDto = AddressDtoFixture::getOneFilled();
        $findExternalAddressMock = $this->createMock(FindExternalAddressInterface::class);
        $findExternalAddressMock
            ->method('find')
            ->willReturn($addressDto);

        $container->set(FindExternalAddressInterface::class, $findExternalAddressMock);

        $checkAddressInRestrictedAreaServiceMock = $this->createMock(CheckAddressInRestrictedAreaService::class);
        $checkAddressInRestrictedAreaServiceMock
            ->method('check')
            ->willReturn(false);

        $container->set(CheckAddressInRestrictedAreaService::class, $checkAddressInRestrictedAreaServiceMock);

        /**
         * Create Country
         */
        $country = CountryFixture::getOne('Russia', 'RU');
        $container->get(CountryRepositoryInterface::class)->create($country);

        /**
         * Create Currency
         */
        $currencyRepository = $container->get(CurrencyRepositoryInterface::class);
        $currencyRepository->create(CurrencyFixture::getOne('RUB', 810, 'Russian ruble'));
        $currency = $currencyRepository->ofCode('RUB');

        $shipmentId = $container->get(CreateShipmentCommandHandler::class)(
            new CreateShipmentCommand(CreateShipmentDtoFixture::getOneFilled(
                $addressDto->address,
                $addressDto->address,
                currencyCode: $currency->getCode()
            ))
        );

        $this->assertNotNull($shipmentId);
        $this->assertInstanceOf(Uuid::class, $shipmentId);

        $shipment = $container->get(ShipmentRepositoryInterface::class)->ofId($shipmentId);

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertEquals($addressDto->address, $shipment->getFrom()->getAddress());
        $this->assertEquals($addressDto->address, $shipment->getTo()->getAddress());
        $this->assertEquals($currency->getCode(), $shipment->getCurrency()->getCode());
    }
}
