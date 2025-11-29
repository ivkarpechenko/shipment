<?php

namespace App\Tests\Application\Region\Query;

use App\Application\Query;
use App\Application\QueryHandler;
use App\Application\Region\Query\GetAllRegionsQuery;
use App\Application\Region\Query\GetAllRegionsQueryHandler;
use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;

class GetAllRegionsQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new GetAllRegionsQuery()
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(GetAllRegionsQueryHandler::class)
        );
    }

    public function testGetAllCountriesQueryHandler()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $repository = $container->get(RegionRepositoryInterface::class);
        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');

        $repository->create($newRegion);

        $regions = $container->get(GetAllRegionsQueryHandler::class)(
            new GetAllRegionsQuery()
        );

        $this->assertNotEmpty($regions);
        $this->assertIsArray($regions);

        $region = reset($regions);

        $this->assertInstanceOf(Region::class, $region);
        $this->assertNotNull($region->getCountry());
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moskva', $region->getName());
        $this->assertEquals('MOW', $region->getCode());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNull($region->getUpdatedAt());
        $this->assertNull($region->getDeletedAt());
    }
}
