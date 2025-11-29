<?php

namespace App\Tests\Application\Address\Command;

use App\Application\Address\Command\RestoreAddressCommand;
use App\Application\Address\Command\RestoreAddressCommandHandler;
use App\Application\Command;
use App\Application\CommandHandler;
use App\Domain\Address\Entity\Address;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\City\Entity\City;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class RestoreAddressCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new RestoreAddressCommand(Uuid::v1())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(RestoreAddressCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new RestoreAddressCommand(Uuid::v1());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testRestoreAddressCommandHandler()
    {
        $container = $this->getContainer();

        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $regionRepository = $container->get(RegionRepositoryInterface::class);
        $cityRepository = $container->get(CityRepositoryInterface::class);
        $addressRepository = $container->get(AddressRepositoryInterface::class);

        $country = CountryFixture::getOne('Russia', 'RU', Uuid::v1());
        $countryRepository->create($country);
        $country = $countryRepository->ofCode('RU');

        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $regionRepository->create($region);
        $region = $regionRepository->ofCode('MOW');

        $city = CityFixture::getOne($region, 'city', 'Moskva');
        $cityRepository->create($city);
        $city = $cityRepository->ofId($city->getId());

        $newAddress = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());
        $newAddress->deleted();
        $addressRepository->create($newAddress);

        $this->assertNotNull($newAddress->getDeletedAt());

        $container->get(RestoreAddressCommandHandler::class)(
            new RestoreAddressCommand($newAddress->getId())
        );

        $address = $container->get(AddressRepositoryInterface::class)->ofAddress($newAddress->getAddress());

        $this->assertNotNull($address);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertNotNull($address->getCity());
        $this->assertInstanceOf(City::class, $address->getCity());
        $this->assertEquals('309850', $address->getPostalCode());
        $this->assertEquals('ул Слободская', $address->getStreet());
        $this->assertEquals('1/1', $address->getHouse());
        $this->assertNotNull($address->getCreatedAt());
        $this->assertNotNull($address->getUpdatedAt());
        $this->assertNull($address->getDeletedAt());

        $deletedAddress = $container->get(AddressRepositoryInterface::class)->ofAddressDeleted($newAddress->getAddress());

        $this->assertNull($deletedAddress);
    }
}
