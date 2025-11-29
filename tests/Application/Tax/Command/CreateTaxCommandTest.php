<?php

namespace App\Tests\Application\Tax\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Tax\Command\CreateTaxCommand;
use App\Application\Tax\Command\CreateTaxCommandHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Tax\Entity\Tax;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\MessageBusTestCase;

class CreateTaxCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $this->assertInstanceOf(
            Command::class,
            new CreateTaxCommand($country->getCode(), 'НДС', 0.2, 'price/(1+value)*value')
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateTaxCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $command = new CreateTaxCommand($country->getCode(), 'НДС', 0.2, 'price/(1+value)*value');
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateTaxCommandHandler()
    {
        $container = $this->getContainer();
        $country = CountryFixture::getOne('Russia', 'RU');
        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);
        $country = $repositoryCountry->ofId($country->getId());

        $container->get(CreateTaxCommandHandler::class)(
            new CreateTaxCommand($country->getCode(), 'НДС', 0.2, 'price/(1+value)*value')
        );

        $this->entityManager->flush();
        $tax = $container->get(TaxRepositoryInterface::class)->ofCountryAndName($country, 'НДС');

        $this->assertNotNull($tax);
        $this->assertInstanceOf(Tax::class, $tax);
        $this->assertNotNull($tax->getCountry());
        $this->assertInstanceOf(Country::class, $tax->getCountry());
        $this->assertEquals('НДС', $tax->getName());
        $this->assertEquals(0.2, $tax->getValue());
        $this->assertEquals('price/(1+value)*value', $tax->getExpression());
        $this->assertNotNull($tax->getCreatedAt());
    }
}
