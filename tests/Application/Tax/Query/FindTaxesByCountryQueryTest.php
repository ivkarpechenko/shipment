<?php

namespace App\Tests\Application\Tax\Query;

use App\Application\Query;
use App\Application\QueryHandler;
use App\Application\Tax\Query\FindTaxesByCountryQuery;
use App\Application\Tax\Query\FindTaxesByCountryQueryHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindTaxesByCountryQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $this->assertInstanceOf(
            Query::class,
            new FindTaxesByCountryQuery($country)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindTaxesByCountryQueryHandler::class)
        );
    }

    public function testFindTaxsByCountryQueryHandler()
    {
        $container = $this->getContainer();

        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $taxRepository = $container->get(TaxRepositoryInterface::class);

        $country = CountryFixture::getOne('Russia', 'RU', Uuid::v1());
        $countryRepository->create($country);

        $country = $countryRepository->ofCode('RU');
        $newTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');
        $taxRepository->create($newTax);

        $country = $countryRepository->ofCode('RU');
        $newTax2 = TaxFixture::getOne($country, 'НДС2', 0.18, 'price/(1+value)*value');
        $taxRepository->create($newTax2);

        $country = $countryRepository->ofCode('RU');
        $taxs = $container->get(FindTaxesByCountryQueryHandler::class)(
            new FindTaxesByCountryQuery($country)
        );

        $this->assertNotEmpty($taxs);
        $this->assertIsArray($taxs);

        $tax = $taxs[0];

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
