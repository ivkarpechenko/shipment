<?php

namespace App\Tests\Application\Tax\Query;

use App\Application\Query;
use App\Application\QueryHandler;
use App\Application\Tax\Query\FindTaxByIdQuery;
use App\Application\Tax\Query\FindTaxByIdQueryHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindTaxByIdQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindTaxByIdQuery(Uuid::v1())
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindTaxByIdQueryHandler::class)
        );
    }

    public function testFindTaxByIdQueryHandler()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $repository = $container->get(TaxRepositoryInterface::class);
        $newTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');

        $repository->create($newTax);

        $tax = $container->get(FindTaxByIdQueryHandler::class)(
            new FindTaxByIdQuery($newTax->getId())
        );

        $this->assertNotNull($tax);
        $this->assertNotNull($tax->getCountry());
        $this->assertInstanceOf(Country::class, $tax->getCountry());
        $this->assertEquals('НДС', $tax->getName());
        $this->assertEquals(0.2, $tax->getValue());
        $this->assertEquals('price/(1+value)*value', $tax->getExpression());
        $this->assertNotNull($tax->getCreatedAt());
    }
}
