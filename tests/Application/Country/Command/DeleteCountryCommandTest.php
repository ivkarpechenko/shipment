<?php

namespace App\Tests\Application\Country\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Country\Command\DeleteCountryCommand;
use App\Application\Country\Command\DeleteCountryCommandHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class DeleteCountryCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new DeleteCountryCommand(Uuid::v1())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(DeleteCountryCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new DeleteCountryCommand(Uuid::v1());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testDeleteCountryCommandHandler()
    {
        $container = $this->getContainer();
        $repository = $container->get(CountryRepositoryInterface::class);
        $newCountry = CountryFixture::getOne('test country', 'RU');

        $repository->create($newCountry);

        $container->get(DeleteCountryCommandHandler::class)(
            new DeleteCountryCommand($newCountry->getId())
        );

        $country = $container->get(CountryRepositoryInterface::class)->ofCode('RU');

        $this->assertNull($country);

        $deletedCountry = $container->get(CountryRepositoryInterface::class)->ofIdDeleted($newCountry->getId());

        $this->assertNotNull($deletedCountry);
        $this->assertInstanceOf(Country::class, $deletedCountry);
        $this->assertEquals('test country', $deletedCountry->getName());
        $this->assertEquals('RU', $deletedCountry->getCode());
        $this->assertNotNull($deletedCountry->getCreatedAt());
        $this->assertNotNull($deletedCountry->getDeletedAt());
        $this->assertNull($deletedCountry->getUpdatedAt());
    }
}
