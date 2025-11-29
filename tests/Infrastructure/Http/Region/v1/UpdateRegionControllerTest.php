<?php

namespace App\Tests\Infrastructure\Http\Region\v1;

use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\HttpTestCase;

class UpdateRegionControllerTest extends HttpTestCase
{
    public function testUpdateRegionNameRoute()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $repository = $container->get(RegionRepositoryInterface::class);
        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');

        $repository->create($newRegion);

        $this->assertEquals('Moskva', $newRegion->getName());

        $this->client->request(
            'PUT',
            "/api/v1/region/{$newRegion->getId()->toRfc4122()}",
            [
                'name' => 'Moskva2',
            ]
        );

        self::assertResponseIsSuccessful();

        $region = $repository->ofId($newRegion->getId());

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertEquals('Moskva2', $region->getName());
        $this->assertEquals('MOW', $region->getCode());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNotNull($region->getUpdatedAt());
        $this->assertNull($region->getDeletedAt());
    }

    public function testUpdateRegionIsActiveRoute()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $repository = $container->get(RegionRepositoryInterface::class);
        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');

        $repository->create($newRegion);

        $this->assertTrue($newRegion->isActive());

        $this->client->request(
            'PUT',
            "/api/v1/region/{$newRegion->getId()->toRfc4122()}",
            [
                'isActive' => false,
            ]
        );

        self::assertResponseIsSuccessful();

        $region = $repository->ofIdDeactivated($newRegion->getId());

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertEquals('Moskva', $region->getName());
        $this->assertEquals('MOW', $region->getCode());
        $this->assertFalse($region->isActive());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNotNull($region->getUpdatedAt());
        $this->assertNull($region->getDeletedAt());
    }
}
