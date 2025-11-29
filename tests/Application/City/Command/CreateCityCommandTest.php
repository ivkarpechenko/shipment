<?php

namespace App\Tests\Application\City\Command;

use App\Application\City\Command\CreateCityCommand;
use App\Application\City\Command\CreateCityCommandHandler;
use App\Application\Command;
use App\Application\CommandHandler;
use App\Domain\City\Entity\City;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;

class CreateCityCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $this->assertInstanceOf(
            Command::class,
            new CreateCityCommand($region->getCode(), 'city', 'Moskva')
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateCityCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $command = new CreateCityCommand($region->getCode(), 'city', 'Moskva');
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateCityCommandHandler()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');
        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);
        $country = $repositoryCountry->ofId($country->getId());

        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $repositoryRegion = $container->get(RegionRepositoryInterface::class);
        $repositoryRegion->create($region);
        $region = $repositoryRegion->ofId($region->getId());

        $container->get(CreateCityCommandHandler::class)(
            new CreateCityCommand($region->getCode(), 'city', 'Moskva')
        );

        $city = $container->get(CityRepositoryInterface::class)->ofTypeAndName('city', 'Moskva');

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertNotNull($city->getRegion());
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('Moskva', $city->getName());
        $this->assertEquals('city', $city->getType());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNull($city->getUpdatedAt());
        $this->assertNull($city->getDeletedAt());
    }
}
