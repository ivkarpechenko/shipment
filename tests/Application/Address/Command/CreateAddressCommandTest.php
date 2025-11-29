<?php

namespace App\Tests\Application\Address\Command;

use App\Application\Address\Command\CreateAddressCommand;
use App\Application\Address\Command\CreateAddressCommandHandler;
use App\Application\Address\Query\External\FindExternalAddressInterface;
use App\Application\Command;
use App\Application\CommandHandler;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\City\Entity\City;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\MessageBusTestCase;

class CreateAddressCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new CreateAddressCommand('309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1')
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateAddressCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new CreateAddressCommand('309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1');
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateAddressCommandHandler()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Россия', 'RU');
        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $service = $this->createMock(FindExternalAddressInterface::class);
        $service->method('find')->willReturn(DaDataAddressDtoFixture::getOne());

        $container->set(FindExternalAddressInterface::class, $service);

        $addressString = '309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1';
        $container->get(CreateAddressCommandHandler::class)(
            new CreateAddressCommand($addressString)
        );

        $address = $container->get(AddressRepositoryInterface::class)->ofAddress($addressString);

        $this->assertNotNull($address->getCity());
        $this->assertInstanceOf(City::class, $address->getCity());
        $this->assertEquals('309850', $address->getPostalCode());
        $this->assertEquals('ул Слободская', $address->getStreet());
        $this->assertEquals('1/1', $address->getHouse());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
    }
}
