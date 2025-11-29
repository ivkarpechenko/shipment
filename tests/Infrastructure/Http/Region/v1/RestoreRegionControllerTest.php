<?php

namespace App\Tests\Infrastructure\Http\Region\v1;

use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\HttpTestCase;

class RestoreRegionControllerTest extends HttpTestCase
{
    public function testRestoreRegionRoute()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());
        $repository = $this->getContainer()->get(RegionRepositoryInterface::class);
        $newRegion = RegionFixture::getOneForDeleted($country, 'Moskva', 'MOW');

        $repository->create($newRegion);

        $this->client->request(
            'POST',
            "/api/v1/region/{$newRegion->getId()->toRfc4122()}/restore"
        );

        self::assertResponseIsSuccessful();

        $restoredRegion = $repository->ofId($newRegion->getId());

        $this->assertNotNull($restoredRegion);
        $this->assertInstanceOf(Region::class, $restoredRegion);
        $this->assertEquals('Moskva', $restoredRegion->getName());
        $this->assertEquals('MOW', $restoredRegion->getCode());
        $this->assertNotNull($restoredRegion->getCreatedAt());
        $this->assertNotNull($restoredRegion->getUpdatedAt());
        $this->assertNull($restoredRegion->getDeletedAt());
    }
}
