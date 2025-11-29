<?php

namespace App\Tests\Application\City\Command;

use App\Application\City\Command\RestoreCityCommand;
use App\Application\City\Command\RestoreCityCommandHandler;
use App\Application\Command;
use App\Application\CommandHandler;
use App\Domain\City\Entity\City;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class RestoreCityCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new RestoreCityCommand(Uuid::v1())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(RestoreCityCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new RestoreCityCommand(Uuid::v1());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testRestoreCityCommandHandler()
    {
        $container = $this->getContainer();

        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $regionRepository = $container->get(RegionRepositoryInterface::class);
        $cityRepository = $container->get(CityRepositoryInterface::class);

        $country = CountryFixture::getOne('Russia', 'RU', Uuid::v1());
        $countryRepository->create($country);
        $country = $countryRepository->ofCode('RU');

        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $regionRepository->create($region);
        $region = $regionRepository->ofCode('MOW');

        $newCity = CityFixture::getOne($region, 'city', 'Moskva');

        $newCity->deleted();

        $cityRepository->create($newCity);

        $this->assertNotNull($newCity->getDeletedAt());

        $container->get(RestoreCityCommandHandler::class)(
            new RestoreCityCommand($newCity->getId())
        );

        $city = $container->get(CityRepositoryInterface::class)->ofTypeAndName('city', 'Moskva');

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertNotNull($city->getRegion());
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('Moskva', $city->getName());
        $this->assertEquals('city', $city->getType());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNotNull($city->getUpdatedAt());
        $this->assertNull($city->getDeletedAt());

        $deletedCity = $container->get(CityRepositoryInterface::class)->ofIdDeleted($newCity->getId());

        $this->assertNull($deletedCity);
    }
}
