<?php

namespace App\Tests\Application\Tax\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Tax\Command\UpdateTaxCommand;
use App\Application\Tax\Command\UpdateTaxCommandHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Tax\Entity\Tax;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateTaxCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new UpdateTaxCommand(Uuid::v1(), 0.2)
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(UpdateTaxCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new UpdateTaxCommand(Uuid::v1(), 0.2);
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testUpdateTaxCommandHandler()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $repository = $container->get(TaxRepositoryInterface::class);
        $newTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');

        $repository->create($newTax);

        $container->get(UpdateTaxCommandHandler::class)(
            new UpdateTaxCommand($newTax->getId(), 0.18)
        );

        $tax = $container->get(TaxRepositoryInterface::class)->ofCountryAndName($country, 'НДС');

        $this->assertNotNull($tax);
        $this->assertInstanceOf(Tax::class, $tax);
        $this->assertNotNull($tax->getCountry());
        $this->assertInstanceOf(Country::class, $tax->getCountry());
        $this->assertEquals('НДС', $tax->getName());
        $this->assertEquals(0.18, $tax->getValue());
        $this->assertEquals('price/(1+value)*value', $tax->getExpression());
        $this->assertNotNull($tax->getCreatedAt());
        $this->assertNotNull($tax->getUpdatedAt());
        $this->assertNull($tax->getDeletedAt());
    }
}
