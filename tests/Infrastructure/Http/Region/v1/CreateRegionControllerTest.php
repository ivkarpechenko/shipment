<?php

namespace App\Tests\Infrastructure\Http\Region\v1;

use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\HttpTestCase;

class CreateRegionControllerTest extends HttpTestCase
{
    public function testCreateRegionRoute()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofCode($country->getCode());

        $this->client->request(
            'POST',
            '/api/v1/region',
            [
                'countryCode' => $country->getCode(),
                'name' => 'Moskva',
                'code' => 'MOW',
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(201);

        $repository = $this->getContainer()->get(RegionRepositoryInterface::class);

        $region = $repository->ofCode('MOW');

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moskva', $region->getName());
        $this->assertEquals('MOW', $region->getCode());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNull($region->getUpdatedAt());
        $this->assertNull($region->getDeletedAt());
    }
}
