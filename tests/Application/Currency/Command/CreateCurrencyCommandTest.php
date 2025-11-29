<?php

namespace App\Tests\Application\Currency\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Currency\Command\CreateCurrencyCommand;
use App\Application\Currency\Command\CreateCurrencyCommandHandler;
use App\Domain\Currency\Entity\Currency;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Tests\Application\MessengerCommandBusTest;

class CreateCurrencyCommandTest extends MessengerCommandBusTest
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new CreateCurrencyCommand('RUB', 810, 'Russian ruble')
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateCurrencyCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new CreateCurrencyCommand('RUB', 810, 'Russian ruble');
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateCurrencyCommandHandler()
    {
        $container = $this->getContainer();
        $container->get(CreateCurrencyCommandHandler::class)(
            new CreateCurrencyCommand('RUB', 810, 'Russian ruble')
        );

        $currency = $container->get(CurrencyRepositoryInterface::class)->ofCode('RUB');

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Russian ruble', $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }
}
