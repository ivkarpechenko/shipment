<?php

namespace App\Tests\Application\Region\Query;

use App\Application\Query;
use App\Application\QueryHandler;
use App\Application\Region\Query\FindRegionsByCountryQuery;
use App\Application\Region\Query\FindRegionsByCountryQueryHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindRegionsByCountryQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $this->assertInstanceOf(
            Query::class,
            new FindRegionsByCountryQuery($country)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindRegionsByCountryQueryHandler::class)
        );
    }

    public function testFindRegionsByCountryQueryHandler()
    {
        $container = $this->getContainer();

        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $regionRepository = $container->get(RegionRepositoryInterface::class);

        $country = CountryFixture::getOne('Russia', 'RU', Uuid::v1());
        $countryRepository->create($country);

        $country = $countryRepository->ofCode('RU');
        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW', Uuid::v1());
        $regionRepository->create($newRegion);

        $country = $countryRepository->ofCode('RU');
        $newRegion2 = RegionFixture::getOne($country, 'Moskva1', 'AMU', Uuid::v1());
        $regionRepository->create($newRegion2);

        $country = $countryRepository->ofCode('RU');
        $regions = $container->get(FindRegionsByCountryQueryHandler::class)(
            new FindRegionsByCountryQuery($country)
        );

        $this->assertNotEmpty($regions);
        $this->assertIsArray($regions);

        $region = $regions[0];

        $this->assertInstanceOf(Region::class, $region);
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moskva', $region->getName());
        $this->assertEquals('MOW', $region->getCode());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNull($region->getUpdatedAt());
        $this->assertNull($region->getDeletedAt());
    }
}
