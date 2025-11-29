<?php

namespace App\Tests\Application\Region\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Region\Command\CreateRegionCommand;
use App\Application\Region\Command\CreateRegionCommandHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\MessageBusTestCase;

class CreateRegionCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $this->assertInstanceOf(
            Command::class,
            new CreateRegionCommand($country->getId(), 'Moskva', 'MOW')
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateRegionCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $command = new CreateRegionCommand($country->getId(), 'Moskva', 'MOW');
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateRegionCommandHandler()
    {
        $container = $this->getContainer();
        $country = CountryFixture::getOne('Russia', 'RU');
        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);
        $country = $repositoryCountry->ofId($country->getId());

        $container->get(CreateRegionCommandHandler::class)(
            new CreateRegionCommand($country->getCode(), 'Moskva', 'MOW')
        );

        $this->entityManager->flush();
        $region = $container->get(RegionRepositoryInterface::class)->ofCode('MOW');

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertNotNull($region->getCountry());
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moskva', $region->getName());
        $this->assertEquals('MOW', $region->getCode());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNull($region->getUpdatedAt());
        $this->assertNull($region->getDeletedAt());
    }
}
