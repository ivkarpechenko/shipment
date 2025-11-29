<?php

namespace App\Tests\Application\Tax\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Tax\Command\DeleteTaxCommand;
use App\Application\Tax\Command\DeleteTaxCommandHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Tax\Entity\Tax;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class DeleteTaxCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new DeleteTaxCommand(Uuid::v1())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(DeleteTaxCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new DeleteTaxCommand(Uuid::v1());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testDeleteTaxCommandHandler()
    {
        $container = $this->getContainer();

        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $taxRepository = $container->get(TaxRepositoryInterface::class);

        $country = CountryFixture::getOne('Russia', 'RU', Uuid::v1());
        $countryRepository->create($country);

        $country = $countryRepository->ofCode('RU');

        $newTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');

        $taxRepository->create($newTax);
        $newTax = $taxRepository->ofCountryAndName($country, 'НДС');

        $container->get(DeleteTaxCommandHandler::class)(
            new DeleteTaxCommand($newTax->getId())
        );

        $tax = $taxRepository->ofCountryAndName($country, 'НДС');

        $this->assertNull($tax);

        $deletedTax = $taxRepository->ofIdDeleted($newTax->getId());

        $this->assertNotNull($deletedTax);
        $this->assertInstanceOf(Tax::class, $deletedTax);
        $this->assertNotNull($deletedTax->getCountry());
        $this->assertInstanceOf(Country::class, $deletedTax->getCountry());
        $this->assertEquals('НДС', $deletedTax->getName());
        $this->assertEquals('0.2', $deletedTax->getValue());
        $this->assertEquals('price/(1+value)*value', $deletedTax->getExpression());
        $this->assertNotNull($deletedTax->getCreatedAt());
        $this->assertNotNull($deletedTax->getDeletedAt());
    }
}
