<?php

namespace App\Tests\Application\Country\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Country\Command\RestoreCountryCommand;
use App\Application\Country\Command\RestoreCountryCommandHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class RestoreCountryCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new RestoreCountryCommand(Uuid::v1())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(RestoreCountryCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new RestoreCountryCommand(Uuid::v1());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testRestoreCountryCommandHandler()
    {
        $container = $this->getContainer();
        $repository = $container->get(CountryRepositoryInterface::class);
        $newCountry = CountryFixture::getOne('test country', 'RU');
        $newCountry->deleted();

        $repository->create($newCountry);

        $this->assertNotNull($newCountry->getDeletedAt());

        $container->get(RestoreCountryCommandHandler::class)(
            new RestoreCountryCommand($newCountry->getId())
        );

        $country = $container->get(CountryRepositoryInterface::class)->ofCode('RU');

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('test country', $country->getName());
        $this->assertEquals('RU', $country->getCode());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNotNull($country->getUpdatedAt());
        $this->assertNull($country->getDeletedAt());

        $deletedCountry = $container->get(CountryRepositoryInterface::class)->ofIdDeleted($newCountry->getId());

        $this->assertNull($deletedCountry);
    }
}
