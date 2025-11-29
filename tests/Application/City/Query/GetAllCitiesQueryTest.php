<?php

namespace App\Tests\Application\City\Query;

use App\Application\City\Query\GetAllCitiesQuery;
use App\Application\City\Query\GetAllCitiesQueryHandler;
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

class GetAllCitiesQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new GetAllCitiesQuery()
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(GetAllCitiesQueryHandler::class)
        );
    }

    public function testGetAllCountriesQueryHandler()
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

        $cities = $container->get(GetAllCitiesQueryHandler::class)(
            new GetAllCitiesQuery()
        );

        $this->assertNotEmpty($cities);
        $this->assertIsArray($cities);

        $city = reset($cities);

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertNotNull($city->getRegion());
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('Moskva', $city->getName());
        $this->assertEquals('city', $city->getType());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNull($city->getUpdatedAt());
        $this->assertNull($city->getDeletedAt());
    }
}
