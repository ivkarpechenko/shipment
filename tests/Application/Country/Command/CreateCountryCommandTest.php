<?php

namespace App\Tests\Application\Country\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Country\Command\CreateCountryCommand;
use App\Application\Country\Command\CreateCountryCommandHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\MessageBusTestCase;

class CreateCountryCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new CreateCountryCommand('test country', 'RU')
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateCountryCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new CreateCountryCommand('test country', 'RU');
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateCountryCommandHandler()
    {
        $container = $this->getContainer();
        $container->get(CreateCountryCommandHandler::class)(
            new CreateCountryCommand('test country', 'RU')
        );

        $country = $container->get(CountryRepositoryInterface::class)->ofCode('RU');

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('test country', $country->getName());
        $this->assertEquals('RU', $country->getCode());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
        $this->assertNull($country->getDeletedAt());
    }
}
