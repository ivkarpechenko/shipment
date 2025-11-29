<?php

namespace App\Tests\Application\Country\Query;

use App\Application\Country\Query\FindCountryByIdQuery;
use App\Application\Country\Query\FindCountryByIdQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindCountryByIdQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindCountryByIdQuery(Uuid::v1())
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindCountryByIdQueryHandler::class)
        );
    }

    public function testFindCountryByIdQueryHandler()
    {
        $container = $this->getContainer();
        $newCountry = CountryFixture::getOne('test country', 'RU');
        $repository = $container->get(CountryRepositoryInterface::class);

        $repository->create($newCountry);

        $country = $container->get(FindCountryByIdQueryHandler::class)(
            new FindCountryByIdQuery($newCountry->getId())
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
