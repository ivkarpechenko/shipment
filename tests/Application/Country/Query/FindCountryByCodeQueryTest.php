<?php

namespace App\Tests\Application\Country\Query;

use App\Application\Country\Query\FindCountryByCodeQuery;
use App\Application\Country\Query\FindCountryByCodeQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\MessageBusTestCase;

class FindCountryByCodeQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindCountryByCodeQuery('RU')
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindCountryByCodeQueryHandler::class)
        );
    }

    public function testFindCountryByCodeQueryHandler()
    {
        $container = $this->getContainer();
        $newCountry = CountryFixture::getOne('test country', 'RU');
        $repository = $container->get(CountryRepositoryInterface::class);

        $repository->create($newCountry);

        $country = $container->get(FindCountryByCodeQueryHandler::class)(
            new FindCountryByCodeQuery($newCountry->getCode())
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
