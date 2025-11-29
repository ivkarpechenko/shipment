<?php

namespace App\Tests\Application\Tax\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Tax\Command\RestoreTaxCommand;
use App\Application\Tax\Command\RestoreTaxCommandHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Tax\Entity\Tax;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class RestoreTaxCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new RestoreTaxCommand(Uuid::v1())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(RestoreTaxCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new RestoreTaxCommand(Uuid::v1());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testRestoreTaxCommandHandler()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $repository = $container->get(TaxRepositoryInterface::class);
        $newTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');

        $newTax->deleted();

        $repository->create($newTax);

        $this->assertNotNull($newTax->getDeletedAt());

        $container->get(RestoreTaxCommandHandler::class)(
            new RestoreTaxCommand($newTax->getId())
        );

        $tax = $container->get(TaxRepositoryInterface::class)->ofCountryAndName($country, 'НДС');

        $this->assertNotNull($tax);
        $this->assertInstanceOf(Tax::class, $tax);
        $this->assertNotNull($tax->getCountry());
        $this->assertInstanceOf(Country::class, $tax->getCountry());
        $this->assertEquals('НДС', $tax->getName());
        $this->assertEquals(0.2, $tax->getValue());
        $this->assertEquals('price/(1+value)*value', $tax->getExpression());
        $this->assertNotNull($tax->getCreatedAt());
        $this->assertNotNull($tax->getUpdatedAt());
        $this->assertNull($tax->getDeletedAt());

        $deletedTax = $container->get(TaxRepositoryInterface::class)->ofIdDeleted($newTax->getId());

        $this->assertNull($deletedTax);
    }
}
