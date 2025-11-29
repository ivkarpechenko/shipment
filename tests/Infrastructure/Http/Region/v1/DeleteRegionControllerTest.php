<?php

namespace App\Tests\Infrastructure\Http\Region\v1;

use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\HttpTestCase;

class DeleteRegionControllerTest extends HttpTestCase
{
    public function testDeleteRegionRoute()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $repository = $container->get(RegionRepositoryInterface::class);
        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');

        $repository->create($newRegion);

        $this->client->request(
            'DElETE',
            "/api/v1/region/{$newRegion->getId()->toRfc4122()}"
        );

        self::assertResponseStatusCodeSame(204);

        $deletedRegion = $repository->ofIdDeleted($newRegion->getId());

        $this->assertNotNull($deletedRegion);
        $this->assertInstanceOf(Region::class, $deletedRegion);
        $this->assertInstanceOf(Country::class, $deletedRegion->getCountry());
        $this->assertEquals('Moskva', $deletedRegion->getName());
        $this->assertEquals('MOW', $deletedRegion->getCode());
        $this->assertNotNull($deletedRegion->getCreatedAt());
        $this->assertNull($deletedRegion->getUpdatedAt());
        $this->assertNotNull($deletedRegion->getDeletedAt());
    }
}
