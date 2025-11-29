<?php

namespace App\Tests\Application\Currency\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Currency\Command\UpdateCurrencyCommand;
use App\Application\Currency\Command\UpdateCurrencyCommandHandler;
use App\Domain\Currency\Entity\Currency;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\MessageBusTestCase;

class UpdateCurrencyCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new UpdateCurrencyCommand('RUB', 'Russian ruble', null)
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(UpdateCurrencyCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new UpdateCurrencyCommand('RUB', 'Russian ruble', null);
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testUpdateCurrencyCommandHandler()
    {
        $container = $this->getContainer();
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');

        $repository = $container->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $container->get(UpdateCurrencyCommandHandler::class)(
            new UpdateCurrencyCommand($newCurrency->getCode(), 'Updated russian ruble', true)
        );

        $currency = $repository->ofCode($newCurrency->getCode());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Updated russian ruble', $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNotNull($currency->getUpdatedAt());

        $currency->changeIsActive(false);
        $repository->update($currency);

        $currency = $repository->ofCodeDeactivated($newCurrency->getCode());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Updated russian ruble', $currency->getName());
        $this->assertFalse($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNotNull($currency->getUpdatedAt());
    }
}
