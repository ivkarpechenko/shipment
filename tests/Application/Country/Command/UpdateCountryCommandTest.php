<?php

namespace App\Tests\Application\Country\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Country\Command\UpdateCountryCommand;
use App\Application\Country\Command\UpdateCountryCommandHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateCountryCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new UpdateCountryCommand(Uuid::v1(), 'test country', true)
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(UpdateCountryCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new UpdateCountryCommand(Uuid::v1(), 'test country', true);
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testUpdateCountryCommandHandler()
    {
        $container = $this->getContainer();
        $repository = $container->get(CountryRepositoryInterface::class);
        $newCountry = CountryFixture::getOne('test country', 'RU');

        $repository->create($newCountry);

        $container->get(UpdateCountryCommandHandler::class)(
            new UpdateCountryCommand($newCountry->getId(), 'updated test country', true)
        );

        $country = $container->get(CountryRepositoryInterface::class)->ofCode('RU');

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('updated test country', $country->getName());
        $this->assertEquals('RU', $country->getCode());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNotNull($country->getUpdatedAt());
        $this->assertNull($country->getDeletedAt());
    }
}
