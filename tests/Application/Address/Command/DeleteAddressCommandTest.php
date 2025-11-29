<?php

namespace App\Tests\Application\Address\Command;

use App\Application\Address\Command\DeleteAddressCommand;
use App\Application\Address\Command\DeleteAddressCommandHandler;
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

class DeleteAddressCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new DeleteAddressCommand(Uuid::v1())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(DeleteAddressCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new DeleteAddressCommand(Uuid::v1());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testDeleteAddressCommandHandler()
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
        $addressRepository->create($newAddress);

        $container->get(DeleteAddressCommandHandler::class)(
            new DeleteAddressCommand($newAddress->getId())
        );

        $address = $addressRepository->ofId($newAddress->getId());

        $this->assertNull($address);

        $deletedAddress = $addressRepository->ofIdDeleted($newAddress->getId());

        $this->assertNotNull($deletedAddress);
        $this->assertInstanceOf(Address::class, $deletedAddress);
        $this->assertNotNull($deletedAddress->getCity());
        $this->assertInstanceOf(City::class, $deletedAddress->getCity());
        $this->assertEquals('309850', $deletedAddress->getPostalCode());
        $this->assertEquals('ул Слободская', $deletedAddress->getStreet());
        $this->assertEquals('1/1', $deletedAddress->getHouse());
        $this->assertNotNull($deletedAddress->getCreatedAt());
        $this->assertNotNull($deletedAddress->getDeletedAt());
        $this->assertNull($deletedAddress->getUpdatedAt());
    }
}
