<?php

namespace App\Tests\Infrastructure\Http\City\v1;

use App\Domain\City\Entity\City;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\HttpTestCase;
use Symfony\Component\Uid\Uuid;

class CreateCityControllerTest extends HttpTestCase
{
    public function testCreateCityRoute()
    {
        $container = $this->getContainer();

        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $regionRepository = $container->get(RegionRepositoryInterface::class);

        $country = CountryFixture::getOne('Russia', 'RU', Uuid::v1());
        $countryRepository->create($country);
        $country = $countryRepository->ofCode('RU');

        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $regionRepository->create($region);
        $region = $regionRepository->ofCode('MOW');

        $this->client->request(
            'POST',
            '/api/v1/city',
            [
                'regionCode' => $region->getCode(),
                'type' => 'city',
                'name' => 'Moskva',
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(201);

        $repository = $this->getContainer()->get(CityRepositoryInterface::class);

        $city = $repository->ofTypeAndName('city', 'Moskva');

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('Moskva', $city->getName());
        $this->assertEquals('city', $city->getType());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNull($city->getUpdatedAt());
        $this->assertNull($city->getDeletedAt());
    }
}
