<?php

namespace App\Tests\Application\Region\Query;

use App\Application\Query;
use App\Application\QueryHandler;
use App\Application\Region\Query\GetRegionsByPaginateQuery;
use App\Application\Region\Query\GetRegionsByPaginateQueryHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class GetRegionsByPaginateQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new GetRegionsByPaginateQuery(1, 1)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(GetRegionsByPaginateQueryHandler::class)
        );
    }

    public function testGetCountriesByPaginateQueryHandler()
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

        $regions = $container->get(GetRegionsByPaginateQueryHandler::class)(
            new GetRegionsByPaginateQuery(0, 2)
        );

        $this->assertNotEmpty($regions);
        $this->assertIsArray($regions);
        $this->assertArrayHasKey('data', $regions);
        $this->assertArrayHasKey('total', $regions);
        $this->assertArrayHasKey('pages', $regions);

        $region = reset($regions['data']);

        $this->assertInstanceOf(Region::class, $region);
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moskva', $region->getName());
        $this->assertEquals('MOW', $region->getCode());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNull($region->getUpdatedAt());
        $this->assertNull($region->getDeletedAt());

        $this->assertEquals(2, $regions['total']);
        $this->assertEquals(1, $regions['pages']);
    }
}
