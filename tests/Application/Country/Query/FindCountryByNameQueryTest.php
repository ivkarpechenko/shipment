<?php

namespace App\Tests\Application\Country\Query;

use App\Application\Country\Query\FindCountryByNameQuery;
use App\Application\Country\Query\FindCountryByNameQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\MessageBusTestCase;

class FindCountryByNameQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindCountryByNameQuery('test country')
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindCountryByNameQueryHandler::class)
        );
    }

    public function testFindCountryByNameQueryHandler()
    {
        $container = $this->getContainer();
        $newCountry = CountryFixture::getOne('test country', 'RU');
        $repository = $container->get(CountryRepositoryInterface::class);

        $repository->create($newCountry);

        $country = $container->get(FindCountryByNameQueryHandler::class)(
            new FindCountryByNameQuery($newCountry->getName())
        );

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('test country', $country->getName());
        $this->assertEquals('RU', $country->getCode());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
        $this->assertNull($country->getDeletedAt());
    }
}
