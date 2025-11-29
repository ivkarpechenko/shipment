<?php

namespace App\Tests\Application\City\Command;

use App\Application\City\Command\DeleteCityCommand;
use App\Application\City\Command\DeleteCityCommandHandler;
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

class DeleteCityCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new DeleteCityCommand(Uuid::v1())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(DeleteCityCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new DeleteCityCommand(Uuid::v1());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testDeleteCityCommandHandler()
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
        $cityRepository->create($newCity);

        $container->get(DeleteCityCommandHandler::class)(
            new DeleteCityCommand($newCity->getId())
        );

        $city = $cityRepository->ofTypeAndName('city', 'Moskva');

        $this->assertNull($city);

        $deletedCity = $cityRepository->ofIdDeleted($newCity->getId());

        $this->assertNotNull($deletedCity);
        $this->assertInstanceOf(City::class, $deletedCity);
        $this->assertNotNull($deletedCity->getRegion());
        $this->assertInstanceOf(Region::class, $deletedCity->getRegion());
        $this->assertEquals('Moskva', $deletedCity->getName());
        $this->assertEquals('city', $deletedCity->getType());
        $this->assertNotNull($deletedCity->getCreatedAt());
        $this->assertNotNull($deletedCity->getDeletedAt());
        $this->assertNull($deletedCity->getUpdatedAt());
    }
}
