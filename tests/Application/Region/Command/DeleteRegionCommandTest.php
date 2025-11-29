<?php

namespace App\Tests\Application\Region\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Region\Command\DeleteRegionCommand;
use App\Application\Region\Command\DeleteRegionCommandHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class DeleteRegionCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new DeleteRegionCommand(Uuid::v1())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(DeleteRegionCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new DeleteRegionCommand(Uuid::v1());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testDeleteRegionCommandHandler()
    {
        $container = $this->getContainer();

        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $regionRepository = $container->get(RegionRepositoryInterface::class);

        $country = CountryFixture::getOne('Russia', 'RU', Uuid::v1());
        $countryRepository->create($country);

        $country = $countryRepository->ofCode('RU');

        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');

        $regionRepository->create($newRegion);
        $newRegion = $regionRepository->ofCode('MOW');

        $container->get(DeleteRegionCommandHandler::class)(
            new DeleteRegionCommand($newRegion->getId())
        );

        $region = $regionRepository->ofCode('MOW');

        $this->assertNull($region);

        $deletedRegion = $regionRepository->ofIdDeleted($newRegion->getId());

        $this->assertNotNull($deletedRegion);
        $this->assertInstanceOf(Region::class, $deletedRegion);
        $this->assertNotNull($deletedRegion->getCountry());
        $this->assertInstanceOf(Country::class, $deletedRegion->getCountry());
        $this->assertEquals('Moskva', $deletedRegion->getName());
        $this->assertEquals('MOW', $deletedRegion->getCode());
        $this->assertNotNull($deletedRegion->getCreatedAt());
        $this->assertNotNull($deletedRegion->getDeletedAt());
        $this->assertNull($deletedRegion->getUpdatedAt());
    }
}
