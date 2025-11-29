<?php

namespace App\Tests\Application\Tax\Query;

use App\Application\Query;
use App\Application\QueryHandler;
use App\Application\Tax\Query\FindTaxByCountryAndNameQuery;
use App\Application\Tax\Query\FindTaxByCountryAndNameQueryHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;
use App\Tests\MessageBusTestCase;

class FindTaxByCountryAndNameQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $this->assertInstanceOf(
            Query::class,
            new FindTaxByCountryAndNameQuery($country, 'НДС')
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindTaxByCountryAndNameQueryHandler::class)
        );
    }

    public function testFindTaxByNameQueryHandler()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $repository = $container->get(TaxRepositoryInterface::class);
        $newTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');

        $repository->create($newTax);

        $tax = $container->get(FindTaxByCountryAndNameQueryHandler::class)(
            new FindTaxByCountryAndNameQuery($country, $newTax->getName())
        );

        $this->assertNotNull($tax);
        $this->assertNotNull($tax->getCountry());
        $this->assertInstanceOf(Country::class, $tax->getCountry());
        $this->assertEquals('НДС', $tax->getName());
        $this->assertEquals(0.2, $tax->getValue());
        $this->assertEquals('price/(1+value)*value', $tax->getExpression());
        $this->assertNotNull($tax->getCreatedAt());
        $this->assertNull($tax->getUpdatedAt());
        $this->assertNull($tax->getDeletedAt());
    }
}
