<?php

namespace App\Tests\Application\Country\Query;

use App\Application\Country\Query\GetCountriesByPaginateQuery;
use App\Application\Country\Query\GetCountriesByPaginateQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\MessageBusTestCase;

class GetCountriesByPaginateQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new GetCountriesByPaginateQuery(1, 1)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(GetCountriesByPaginateQueryHandler::class)
        );
    }

    public function testGetCountriesByPaginateQueryHandler()
    {
        $container = $this->getContainer();
        $newCountry = CountryFixture::getOne('test country', 'RU');
        $newCountry2 = CountryFixture::getOne('test country 2', 'KZ');
        $repository = $container->get(CountryRepositoryInterface::class);

        $repository->create($newCountry);
        $repository->create($newCountry2);

        $countries = $container->get(GetCountriesByPaginateQueryHandler::class)(
            new GetCountriesByPaginateQuery(0, 2)
        );

        $this->assertNotEmpty($countries);
        $this->assertIsArray($countries);
        $this->assertArrayHasKey('data', $countries);
        $this->assertArrayHasKey('total', $countries);
        $this->assertArrayHasKey('pages', $countries);

        $country = reset($countries['data']);

        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('test country', $country->getName());
        $this->assertEquals('RU', $country->getCode());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
        $this->assertNull($country->getDeletedAt());

        $this->assertEquals(2, $countries['total']);
        $this->assertEquals(1, $countries['pages']);
    }
}
