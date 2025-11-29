<?php

namespace App\Tests\Application\Country\Query;

use App\Application\Country\Query\GetAllCountriesQuery;
use App\Application\Country\Query\GetAllCountriesQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\MessageBusTestCase;

class GetAllCountriesQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new GetAllCountriesQuery()
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(GetAllCountriesQueryHandler::class)
        );
    }

    public function testGetAllCountriesQueryHandler()
    {
        $container = $this->getContainer();
        $newCountry = CountryFixture::getOne('test country', 'RU');
        $repository = $container->get(CountryRepositoryInterface::class);

        $repository->create($newCountry);

        $countries = $container->get(GetAllCountriesQueryHandler::class)(
            new GetAllCountriesQuery()
        );

        $this->assertNotEmpty($countries);
        $this->assertIsArray($countries);

        $country = reset($countries);

        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('test country', $country->getName());
        $this->assertEquals('RU', $country->getCode());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
        $this->assertNull($country->getDeletedAt());
    }
}
