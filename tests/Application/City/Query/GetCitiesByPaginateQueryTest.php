<?php

namespace App\Tests\Application\City\Query;

use App\Application\City\Query\GetCitiesByPaginateQuery;
use App\Application\City\Query\GetCitiesByPaginateQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\City\Entity\City;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class GetCitiesByPaginateQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new GetCitiesByPaginateQuery(1, 1)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(GetCitiesByPaginateQueryHandler::class)
        );
    }

    public function testGetCountriesByPaginateQueryHandler()
    {
        $container = $this->getContainer();

        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $regionRepository = $container->get(RegionRepositoryInterface::class);
        $cityRepository = $container->get(CityRepositoryInterface::class);

        $country = CountryFixture::getOne('Russia', 'RU', Uuid::v1());
        $countryRepository->create($country);
        $country = $countryRepository->ofCode('RU');

        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $regionRepository->create($region);
        $region = $regionRepository->ofCode('MOW');

        $newCity = CityFixture::getOne($region, 'city', 'Moskva');
        $cityRepository->create($newCity);

        $region = $regionRepository->ofCode('MOW');
        $newCity = CityFixture::getOne($region, 'village', 'Moskva2');
        $cityRepository->create($newCity);

        $cities = $container->get(GetCitiesByPaginateQueryHandler::class)(
            new GetCitiesByPaginateQuery(0, 2)
        );

        $this->assertNotEmpty($cities);
        $this->assertIsArray($cities);
        $this->assertArrayHasKey('data', $cities);
        $this->assertArrayHasKey('total', $cities);
        $this->assertArrayHasKey('pages', $cities);

        $city = reset($cities['data']);

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertNotNull($city->getRegion());
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('Moskva', $city->getName());
        $this->assertEquals('city', $city->getType());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNull($city->getUpdatedAt());
        $this->assertNull($city->getDeletedAt());

        $this->assertEquals(2, $cities['total']);
        $this->assertEquals(1, $cities['pages']);
    }
}
